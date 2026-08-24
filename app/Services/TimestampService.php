<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TimestampService
{
    private string $tsaUrl;

    private string $hashAlgorithm;

    public function __construct(?string $tsaUrl = null, ?string $hashAlgorithm = null)
    {
        $this->tsaUrl = $tsaUrl ?? config('custom.prescription.signature.tsa.url', 'http://timestamp.digicert.com');
        $this->hashAlgorithm = $hashAlgorithm ?? config('custom.prescription.signature.tsa.hash_algorithm', 'sha256');
    }

    /**
     * Get a timestamp token for the given data.
     */
    public function timestamp(string $data): ?string
    {
        try {
            $imprint = $this->calculateImprint($data);
            $request = $this->createTimestampRequest($imprint);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/timestamp-query',
                    'Accept' => 'application/timestamp-reply',
                ])
                ->withBody($request, 'application/octet-stream')
                ->post($this->tsaUrl);

            if (! $response->successful()) {
                Log::error('TSA request failed', [
                    'status' => $response->status(),
                    'url' => $this->tsaUrl,
                ]);

                return null;
            }

            return $this->parseTimestampResponse($response->body());
        } catch (\Exception $e) {
            Log::error('TSA timestamp failed', [
                'error' => $e->getMessage(),
                'url' => $this->tsaUrl,
            ]);

            return null;
        }
    }

    /**
     * Calculate the imprint (hash) of the data.
     */
    private function calculateImprint(string $data): string
    {
        return hash($this->hashAlgorithm, $data, true);
    }

    /**
     * Create an ASN.1 TIMESTAMP-REQUEST.
     *
     * RFC 3161 Section 2.4.1:
     * TimestampRequest ::= SEQUENCE {
     *   version          INTEGER v1(1),
     *   messageImprint   SEQUENCE {
     *     hashAlgorithm  AlgorithmIdentifier,
     *     hashedMessage  OCTET STRING
     *   },
     *   reqPolicy        TSTPolicyId OPTIONAL,
     *   nonce            INTEGER OPTIONAL,
     *   certReq          BOOLEAN DEFAULT FALSE,
     *   extensions       [0] IMPLICIT Extensions OPTIONAL
     * }
     */
    private function createTimestampRequest(string $imprint): string
    {
        $hashAlgorithmOid = $this->getHashAlgorithmOid();

        // Build the hashAlgorithm SEQUENCE
        $hashAlgorithm = $this->encodeSequence(
            $this->encodeOid($hashAlgorithmOid),
            $this->encodeNull()
        );

        // Build the messageImprint SEQUENCE
        $messageImprint = $this->encodeSequence(
            $hashAlgorithm,
            $this->encodeOctetString($imprint)
        );

        // Build the TIMESTAMP-REQUEST SEQUENCE
        $request = $this->encodeSequence(
            $this->encodeInteger(1), // version v1
            $messageImprint,
            $this->encodeBoolean(true) // certReq = TRUE
        );

        return $request;
    }

    /**
     * Parse the ASN.1 TIMESTAMP-RESPONSE and extract the timestamp token.
     *
     * RFC 3161 Section 2.4.2:
     * TimestampResponse ::= SEQUENCE {
     *   status            PKIStatusInfo,
     *   timeStampToken    TimeStampToken OPTIONAL
     * }
     */
    private function parseTimestampResponse(string $response): ?string
    {
        $pos = 0;
        $sequence = $this->parseSequence($response, $pos);

        if ($sequence === null || count($sequence) < 2) {
            return null;
        }

        // The second element is the timeStampToken
        $token = $sequence[1];

        // If token is an array (nested sequence), convert it back to binary
        if (is_array($token)) {
            $token = $this->encodeSequence(...array_filter($token, fn ($item) => $item !== null));
        }

        // Check if it's an ASN.1 EXPLICIT tag [0] (context-specific)
        if (is_string($token) && strlen($token) > 0 && ord($token[0]) === 0xA0) {
            // Extract the content (skip tag and length)
            $token = substr($token, 2);
        }

        return is_string($token) ? $token : null;
    }

    /**
     * Get the OID for the hash algorithm.
     */
    private function getHashAlgorithmOid(): string
    {
        return match ($this->hashAlgorithm) {
            'sha256' => '2.16.840.1.101.3.4.2.1',
            'sha384' => '2.16.840.1.101.3.4.2.2',
            'sha512' => '2.16.840.1.101.3.4.2.3',
            'sha1' => '1.3.14.3.2.26',
            default => '2.16.840.1.101.3.4.2.1', // SHA-256
        };
    }

    // ============================================
    // ASN.1 Encoding Functions
    // ============================================

    private function encodeSequence(string ...$items): string
    {
        $content = implode('', $items);

        return "\x30".$this->encodeLength(strlen($content)).$content;
    }

    private function encodeInteger(int $value): string
    {
        $bytes = '';
        $value = max(0, $value);

        if ($value === 0) {
            $bytes = "\x00";
        } else {
            while ($value > 0) {
                $bytes = chr($value & 0xFF).$bytes;
                $value >>= 8;
            }
        }

        // Add leading zero if high bit is set
        if (ord($bytes[0]) & 0x80) {
            $bytes = "\x00".$bytes;
        }

        return "\x02".$this->encodeLength(strlen($bytes)).$bytes;
    }

    private function encodeOctetString(string $value): string
    {
        return "\x04".$this->encodeLength(strlen($value)).$value;
    }

    private function encodeOid(string $oid): string
    {
        $parts = explode('.', $oid);
        $bytes = chr((int) ($parts[0] * 40 + (int) $parts[1]));

        for ($i = 2; $i < count($parts); $i++) {
            $value = (int) $parts[$i];
            $temp = chr($value & 0x7F);
            $value >>= 7;

            while ($value > 0) {
                $temp = chr(($value & 0x7F) | 0x80).$temp;
                $value >>= 7;
            }

            $bytes .= $temp;
        }

        return "\x06".$this->encodeLength(strlen($bytes)).$bytes;
    }

    private function encodeNull(): string
    {
        return "\x05\x00";
    }

    private function encodeBoolean(bool $value): string
    {
        return "\x01\x01".($value ? "\xff" : "\x00");
    }

    private function encodeLength(int $length): string
    {
        if ($length < 0x80) {
            return chr($length);
        }

        $bytes = '';
        while ($length > 0) {
            $bytes = chr($length & 0xFF).$bytes;
            $length >>= 8;
        }

        return chr(0x80 | strlen($bytes)).$bytes;
    }

    // ============================================
    // ASN.1 Parsing Functions
    // ============================================

    private function parseSequence(string $data, int &$pos): ?array
    {
        if ($pos >= strlen($data)) {
            return null;
        }

        $tag = ord($data[$pos]);
        if ($tag !== 0x30) {
            return null;
        }
        $pos++;

        $length = $this->parseLength($data, $pos);
        if ($length === null) {
            return null;
        }

        $endPos = $pos + $length;
        $items = [];

        while ($pos < $endPos) {
            $itemTag = ord($data[$pos]);

            if ($itemTag === 0x30) {
                // Nested sequence
                $items[] = $this->parseSequence($data, $pos);
            } elseif ($itemTag === 0xA0) {
                // Context-specific tag [0]
                $pos++;
                $itemLength = $this->parseLength($data, $pos);
                if ($itemLength !== null) {
                    $items[] = substr($data, $pos, $itemLength);
                    $pos += $itemLength;
                }
            } else {
                // Other TLV
                $pos++;
                $itemLength = $this->parseLength($data, $pos);
                if ($itemLength !== null) {
                    $items[] = substr($data, $pos, $itemLength);
                    $pos += $itemLength;
                }
            }
        }

        return $items;
    }

    private function parseLength(string $data, int &$pos): ?int
    {
        if ($pos >= strlen($data)) {
            return null;
        }

        $byte = ord($data[$pos]);
        $pos++;

        if ($byte < 0x80) {
            return $byte;
        }

        $numBytes = $byte & 0x7F;
        $length = 0;

        for ($i = 0; $i < $numBytes; $i++) {
            if ($pos >= strlen($data)) {
                return null;
            }
            $length = ($length << 8) | ord($data[$pos]);
            $pos++;
        }

        return $length;
    }
}
