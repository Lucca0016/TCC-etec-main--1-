import React, { useState } from 'react';

export const Login: React.FC = () => {
  const [email, setEmail] = useState('');
  const [senha, setSenha] = useState('');
  const [carregando, setCarregando] = useState(false);
  const [erro, setErro] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setCarregando(true);
    setErro('');

    try {
      const formulario = new FormData();
      formulario.append('email', email);
      formulario.append('senha', senha);

      const resposta = await fetch('/TCC-etec/login', {
        method: 'POST',
        body: formulario,
        credentials: 'same-origin',
      });

      if (resposta.ok) {
        window.location.href = '/TCC-etec/admin';
      } else {
        setErro('Email ou senha inválidos');
      }
    } catch (e) {
      setErro('Erro ao conectar ao servidor');
      console.error(e);
    } finally {
      setCarregando(false);
    }
  };

  return (
    <div className="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-500 to-blue-700">
      <div className="w-full max-w-md">
        <div className="bg-white rounded-lg shadow-2xl p-8">
          <h1 className="text-3xl font-bold text-center text-gray-800 mb-2">
            FETEL
          </h1>
          <p className="text-center text-gray-600 mb-8">
            Sistema de Gestão de Biblioteca
          </p>

          {erro && (
            <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
              {erro}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-gray-700 font-semibold mb-2">
                Email
              </label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                disabled={carregando}
              />
            </div>

            <div>
              <label className="block text-gray-700 font-semibold mb-2">
                Senha
              </label>
              <input
                type="password"
                value={senha}
                onChange={(e) => setSenha(e.target.value)}
                required
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                disabled={carregando}
              />
            </div>

            <button
              type="submit"
              className="w-full px-4 py-2 text-white font-semibold bg-blue-600 hover:bg-blue-700 rounded-lg transition-all disabled:opacity-50"
              disabled={carregando}
            >
              {carregando ? 'Conectando...' : 'Entrar'}
            </button>
          </form>

          <p className="text-center text-gray-600 text-sm mt-6">
            © 2026 FETEL - Todos os direitos reservados
          </p>
        </div>
      </div>
    </div>
  );
};
