import React, { useEffect, useState } from 'react';
import { Login } from './pages/Login';
import { AdminDashboard } from './pages/AdminDashboard';

export const App: React.FC = () => {
  const [autenticado, setAutenticado] = useState<boolean | null>(null);

  useEffect(() => {
    verificarAutenticacao();
  }, []);

  const verificarAutenticacao = async () => {
    try {
      const resposta = await fetch('/TCC-etec/api/auth/check', {
        credentials: 'same-origin',
      });

      if (resposta.ok) {
        const dados = await resposta.json();
        setAutenticado(dados.autenticado === true);
      } else {
        setAutenticado(false);
      }
    } catch {
      setAutenticado(false);
    }
  };

  if (autenticado === null) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-gray-100">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
          <p className="text-gray-600 font-medium">Carregando...</p>
        </div>
      </div>
    );
  }

  return autenticado ? <AdminDashboard /> : <Login />;
};
