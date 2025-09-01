import Image from "next/image";
import Link from "next/link";
import logo from "../../../assets/LogoFESA.svg";
import {
  Linkedin,
  Twitter,
  Facebook,
  Instagram,
  MessageCircle,
} from "lucide-react";

export default function Footer() {
  return (
    <footer className="bg-white pt-8 pb-4">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Logo column */}
          <div className="flex flex-col items-center md:items-start justify-between">
            <Image
              src={logo}
              alt="Farmacias Especializadas FESA"
              width={180}
              height={45}
              className="h-auto w-auto object-contain mb-6"
            />
            <div className="flex space-x-4 mt-2">
              <Link
                href="https://www.linkedin.com/company/farmacias-especializadas/"
                className="text-blue-fesa "
              >
                <Linkedin className="h-5 w-5" />
              </Link>
              <Link
                href="https://x.com/FarmaciasEsp"
                className="text-blue-fesa "
              >
                <Twitter className="h-5 w-5" />
              </Link>
              <Link
                href="https://www.facebook.com/profile.php?id=61571925606792&mibextid=wwXIfr&rdid=wPOrjOlQV2hRiOwn&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1EwzMUryGK%2F%3Fmibextid%3DwwXIfr#"
                className="text-blue-fesa "
              >
                <Facebook className="h-5 w-5" />
              </Link>
              <Link
                href="https://www.instagram.com/farmaciasesp"
                className="text-blue-fesa "
              >
                <Instagram className="h-5 w-5" />
              </Link>
              <Link
                href="https://api.whatsapp.com/send?phone=525555883372&text=%C2%A1Hola!%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20de..."
                className="text-blue-fesa "
              >
                <MessageCircle className="h-5 w-5" />
              </Link>
            </div>
          </div>

          {/* Servicios en Línea */}
          <div>
            <h3 className="font-bold text-blue-fesa mb-4">
              Servicios en Línea
            </h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/fesa-en-linea"
                  className="text-gray-600 hover:text-blue-500"
                >
                  FESA en línea
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/requisitos-receta-fesa"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Requisitos de Receta
                </Link>
              </li>
              <li>
                <Link
                  href="tel:5552844500"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Llamar a Bancos y Aseguradoras
                </Link>
              </li>
              <li>
                <Link
                  href="https://facturacion.farmaciasespecializadas.com/"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Factura en línea
                </Link>
              </li>
              <li>
                <Link
                  href="https://clientes.farmaciaespecializada.com/"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Clientes FESA
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/telefarmacos"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Telefármacos
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/contact"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Contáctanos
                </Link>
              </li>
            </ul>
          </div>

          {/* FESA */}
          <div>
            <h3 className="font-bold text-blue-fesa mb-4">FESA</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/quienes-somos-fesa"
                  className="text-gray-600 hover:text-blue-500"
                >
                  ¿Quiénes somos?
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/trabaja-con-nosotros"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Trabaja con Nosotros
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/sucursales-fesa"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Encuentra tu Farmacia
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/programas-apoyo"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Programas de Apoyo
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/fesa-te-acompana"
                  className="text-gray-600 hover:text-blue-500"
                >
                  FESA te acompaña
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/media/wysiwyg/pdf/DescuenTOPS_Marzo.pdf"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Boletín de Ofertas
                </Link>
              </li>
            </ul>
          </div>

          {/* Tiendas Oficiales */}
          <div>
            <h3 className="font-bold text-blue-fesa mb-4">Tiendas Oficiales</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/ferring-store"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Ferring
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/pfizer-store"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Pfizer
                </Link>
              </li>
              <li>
                <Link
                  href="https://www.farmaciasespecializadas.com/sunpharma"
                  className="text-gray-600 hover:text-blue-500"
                >
                  Sun Pharma
                </Link>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom footer */}
        <div className="border-t mt-8 pb-20 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-600">
          <div className="flex space-x-4 mb-4 md:mb-0">
            <Link href="#" className="hover:text-blue-500">
              Términos y Condiciones
            </Link>
            <Link href="#" className="hover:text-blue-500">
              Aviso de Privacidad
            </Link>
          </div>
          <div>
            © FESA {new Date().getFullYear()}. Todos los derechos reservados.
          </div>
        </div>
      </div>
    </footer>
  );
}
