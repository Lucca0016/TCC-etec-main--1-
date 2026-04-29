(function(){const listaRelacoes=document.createElement("link").relList;
  if(listaRelacoes&&listaRelacoes.supports&&listaRelacoes.supports("modulepreload"))
    return;
  for(const elementoLink of document.querySelectorAll('link[rel="modulepreload"]'))processarModulePreload(elementoLink);
  new MutationObserver(listaMutacoes=>{for(const mutacao of listaMutacoes)
    if(mutacao.type==="childList")
      for(const noAdicionado of mutacao.addedNodes)noAdicionado.tagName==="LINK"&&noAdicionado.rel==="modulepreload"&&processarModulePreload(noAdicionado)}).observe(document,{childList:!0,subtree:!0});
    function construirOpcoesFetch(elemento){const opcoes={};
    if(elemento.integrity)opcoes.integrity=elemento.integrity;
if(elemento.referrerPolicy)opcoes.referrerPolicy=elemento.referrerPolicy;opcoes.credentials=elemento.crossOrigin==="use-credentials"?"include":elemento.crossOrigin==="anonymous"?"omit":"same-origin";
return opcoes}function processarModulePreload(elemento){if(elemento.ep)
  return;elemento.ep=!0;const opcoes=construirOpcoesFetch(elemento);fetch(elemento.href,opcoes)}})();
  const elementoApp=document.getElementById("app");elementoApp&&(elementoApp.innerHTML=`
    <main class="p-6">
      <h1 class="text-2xl font-bold">FETEL — Portal</h1>
      <p class="mt-2 text-gray-700">Front-end em TypeScript + Tailwind (build para app/public/frontend)</p>
    </main>
  `);
