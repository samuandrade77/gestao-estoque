// Parte de consulta, alteracao e exclusao do CRUD de usuarios
const formulario = document.querySelector("form");
const tabela = document.getElementById("lista_usuarios");

async function listarUsuarios() {
    const resposta = await requisitar("lista_usuarios.php");

    if (!resposta.sucesso) {
        mostrarResposta(resposta);
        return;
    }

    tabela.textContent = "";

    resposta.dados.forEach(function (usuario) {
        const linha = tabela.insertRow();

        [usuario.id_usuario, usuario.nome, usuario.email, usuario.perfil].forEach(function (valor) {
            linha.insertCell().textContent = valor;
        });

        const acoes = linha.insertCell();
        acoes.appendChild(criarBotao("Editar", function () { editarUsuario(usuario); }));
        acoes.appendChild(criarBotao("Excluir", function () { excluirUsuario(usuario); }));
    });
}

function criarBotao(texto, acao) {
    const botao = document.createElement("button");
    botao.type = "button";
    botao.textContent = texto;
    botao.addEventListener("click", acao);
    return botao;
}

function editarUsuario(usuario) {
    formulario.reset();
    formulario.elements["id_usuario"].value = usuario.id_usuario;
    formulario.elements["nome"].value = usuario.nome;
    formulario.elements["email"].value = usuario.email;
    formulario.elements["perfil"].value = usuario.perfil;
}

async function excluirUsuario(usuario) {
    if (!confirm("Excluir o usuario " + usuario.nome + "?")) {
        return;
    }

    const campos = new FormData();
    campos.append("id_usuario", usuario.id_usuario);

    mostrarResposta(await requisitar("exclui_usuario.php", { method: "POST", body: campos }));
    limparFormulario();
    listarUsuarios();
}

function limparFormulario() {
    formulario.reset();
    formulario.elements["id_usuario"].value = "";
}

document.getElementById("limpar").addEventListener("click", limparFormulario);

formulario.addEventListener("enviado", function () {
    limparFormulario();
    listarUsuarios();
});

listarUsuarios();
