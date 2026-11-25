// Usado nas paginas protegidas: confere no servidor se existe usuario logado.
(async function () {
    const resposta = await requisitar("verifica_sessao.php");
    const area = document.getElementById("usuario_logado");

    if (resposta.sucesso && area) {
        area.textContent = "Usuario logado: " + resposta.dados.nome + " (" + resposta.dados.perfil + ")";
    }
})();

const botaoSair = document.getElementById("sair");

if (botaoSair) {
    botaoSair.addEventListener("click", async function () {
        await requisitar("logout.php", { method: "POST" });
        window.location.href = "index.html";
    });
}
