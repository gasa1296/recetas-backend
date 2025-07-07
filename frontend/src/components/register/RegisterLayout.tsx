import { ReactNode } from "react";
import AuthDesignLayout from "@/components/Layouts/AuthDesignLayout";

interface RegisterLayoutProps {
  children: ReactNode;
}

export const RegisterLayout = ({ children }: RegisterLayoutProps) => {
  return (
    <AuthDesignLayout>
      <main className="container mx-auto px-4 py-8">
        <div className="grid md:grid-cols-12 gap-6">{children}</div>
      </main>
    </AuthDesignLayout>
  );
};
