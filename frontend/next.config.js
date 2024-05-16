/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  images: {
    domains: [
      "receta.farmaciasespecializadas.com",
      "s3-repositorio-cloudseus.s3.amazonaws.com",
      "apireceta.farmaciasespecializadas.com",
      "testapireceta.farmaciasespecializadas.com",
      "aplicacion.farmaciasespecializadas.com",
    ],
  },
};

module.exports = nextConfig;
