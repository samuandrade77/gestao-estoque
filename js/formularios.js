// Envia os formularios por JavaScript (fetch) e mostra a resposta JSON na propria pagina.
// Todos os caminhos sao relativos: o sistema funciona em qualquer pasta do htdocs.

function mostrarResposta(resposta) {
    const area = document.getElementById("resultado");
    area.textContent = "";

    const mensagem = document.createElement("p");
    mensagem.textContent = resposta.mensagem;
    area.appendChild(mensagem);

    // Consultas e relatorios devolvem uma lista de registros
    if (Array.isArray(resposta.dados)) {
        resposta.dados.forEach(function (registro) {
            for (const campo in registro) {
                const linha = document.createElement("div");
                linha.textContent = campo + ": " + (registro[campo] ?? "");
                area.appendChild(linha);
            }
            area.appendChild(document.createElement("hr"));
        });
    }
}

async function requisitar(endereco, opcoes) {
    try {
        const retorno = await fetch(endereco, opcoes);

        // 401 = sem login: volta para a tela de login
        if (retorno.status === 401) {
            window.location.href = "index.html";
        }
        return await retorno.json();
    } catch (erro) {
        return { sucesso: false, mensagem: "Nao foi possivel comunicar com o servidor.", dados: [] };
    }
}

document.querySelectorAll("form").forEach(function (formulario) {
    formulario.addEventListener("submit", async function (evento) {
        evento.preventDefault(); // impede o envio tradicional (recarregar a pagina)

        let endereco = formulario.getAttribute("action");
        const metodo = formulario.getAttribute("method").toUpperCase();
        const campos = new FormData(formulario);
        const opcoes = { method: metodo };

        if (metodo === "GET") {
            endereco += "?" + new URLSearchParams(campos).toString();
        } else {
            opcoes.body = campos;
        }

        const resposta = await requisitar(endereco, opcoes);
        mostrarResposta(resposta);

        // Avisa outros scripts que o envio deu certo
        if (resposta.sucesso) {
            formulario.dispatchEvent(new CustomEvent("enviado", { detail: resposta }));
        }
    });
});
