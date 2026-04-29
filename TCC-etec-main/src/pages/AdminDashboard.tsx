import React from 'react';
import { Usuario, UsuarioForm, ApiResponse } from '../types/api';
import { api } from '../types/client';
import { Botao } from '../components/Botao';

export const AdminDashboard: React.FC = () => {
  const [usuarios, setUsuarios] = React.useState<Usuario[]>([]);
  const [carregando, setCarregando] = React.useState(false);
  const [formularioAberto, setFormularioAberto] = React.useState(false);
  const [novoUsuario, setNovoUsuario] = React.useState<Partial<UsuarioForm>>({});

  React.useEffect(() => {
    carregarUsuarios();
  }, []);

  const carregarUsuarios = async () => {
    setCarregando(true);
    try {
      const resposta = await api.get<Usuario[]>('/usuarios');
      setUsuarios(resposta);
    } catch (erro) {
      console.error('Erro ao carregar usuários:', erro);
    } finally {
      setCarregando(false);
    }
  };

  const criarUsuario = async () => {
    if (!novoUsuario.email || !novoUsuario.nome_completo) {
      alert('Email e nome são obrigatórios');
      return;
    }

    try {
      await api.post<ApiResponse<Usuario>>('/usuarios', novoUsuario);
      setNovoUsuario({});
      setFormularioAberto(false);
      await carregarUsuarios();
    } catch (erro) {
      console.error('Erro ao criar usuário:', erro);
    }
  };

  const deletarUsuario = async (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este usuário?')) return;
    try {
      await api.delete(`/usuarios/${id}`);
      await carregarUsuarios();
    } catch (erro) {
      console.error('Erro ao deletar usuário:', erro);
    }
  };

  return (
    <div className="container-principal">
      <div className="mb-8">
        <h1 className="text-3xl font-bold mb-4">Gerenciar Usuários</h1>
        <Botao
          variante="primario"
          onClick={() => setFormularioAberto(!formularioAberto)}
        >
          {formularioAberto ? 'Cancelar' : 'Novo Usuário'}
        </Botao>
      </div>

      {formularioAberto && (
        <div className="card mb-8">
          <div className="grid gap-4">
            <input
              type="email"
              placeholder="Email"
              className="input-padrao"
              value={novoUsuario.email || ''}
              onChange={(e) => setNovoUsuario({ ...novoUsuario, email: e.target.value })}
            />
            <input
              type="text"
              placeholder="Nome Completo"
              className="input-padrao"
              value={novoUsuario.nome_completo || ''}
              onChange={(e) =>
                setNovoUsuario({ ...novoUsuario, nome_completo: e.target.value })
              }
            />
            <select
              className="input-padrao"
              value={novoUsuario.papel || 'aluno'}
              onChange={(e) =>
                setNovoUsuario({
                  ...novoUsuario,
                  papel: e.target.value as UsuarioForm['papel'],
                })
              }
            >
              <option value="aluno">Aluno</option>
              <option value="secretaria">Secretaria</option>
              <option value="professor">Professor</option>
              <option value="admin">Administrador</option>
            </select>
            <Botao variante="primario" onClick={criarUsuario}>
              Salvar Usuário
            </Botao>
          </div>
        </div>
      )}

      {carregando ? (
        <p className="text-center text-gray-500">Carregando usuários...</p>
      ) : (
        <div className="card">
          <table className="tabela-padrao">
            <thead>
              <tr>
                <th>Email</th>
                <th>Nome</th>
                <th>Papel</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              {usuarios.map((usuario) => (
                <tr key={usuario.id}>
                  <td>{usuario.email}</td>
                  <td>{usuario.nome_completo}</td>
                  <td className="capitalize">{usuario.papel}</td>
                  <td>
                    <Botao
                      variante="perigo"
                      tamanho="pequeno"
                      onClick={() => deletarUsuario(usuario.id)}
                    >
                      Deletar
                    </Botao>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
};
