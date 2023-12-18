import AuthLayout from "@/components/Layouts/AuthLayout";
import Home from "@/components/pages/home";
export default function HomePage() {
    return (
        <main>
            <AuthLayout>
                <Home />
            </AuthLayout>
        </main>
    );
}
