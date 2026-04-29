import React from 'react';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variante?: 'primario' | 'secundario' | 'perigo';
  tamanho?: 'pequeno' | 'medio' | 'grande';
  carregando?: boolean;
  children: React.ReactNode;
}

export const Botao: React.FC<ButtonProps> = ({
  variante = 'primario',
  tamanho = 'medio',
  carregando = false,
  children,
  disabled,
  className,
  ...props
}) => {
  const estiloVariante = {
    primario: 'bg-blue-600 hover:bg-blue-700 text-white',
    secundario: 'bg-gray-200 hover:bg-gray-300 text-gray-900',
    perigo: 'bg-red-600 hover:bg-red-700 text-white',
  }[variante];

  const estiloTamanho = {
    pequeno: 'px-3 py-1 text-sm',
    medio: 'px-4 py-2 text-base',
    grande: 'px-6 py-3 text-lg',
  }[tamanho];

  return (
    <button
      disabled={disabled || carregando}
      className={`
        rounded-lg font-medium transition-all
        disabled:opacity-50 disabled:cursor-not-allowed
        ${estiloVariante}
        ${estiloTamanho}
        ${className || ''}
      `}
      {...props}
    >
      {carregando ? '⏳ Processando...' : children}
    </button>
  );
};
