document.addEventListener("DOMContentLoaded", () => {
    const lista = document.getElementById("listaPedidos");

    // ====== MODAL PERSONALIZADO PARA TOKEN ======
    const overlay = document.createElement("div");
    overlay.className = "overlay-token hidden-token";
    overlay.innerHTML = `
        <div class="box-token">
            <h2>Finalizar entrega</h2>
            <p>Digite o token informado pelo cliente:</p>
            <input type="text" id="campoToken" maxlength="6" class="campo-token" />
            <small id="erroToken" class="erro-token"></small>
            <div class="botoes-token">
                <button type="button" id="btnCancelarToken" class="btn-token btn-cancelar">Cancelar</button>
                <button type="button" id="btnConfirmarToken" class="btn-token btn-confirmar">Finalizar</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    const campoToken      = document.getElementById("campoToken");
    const erroToken       = document.getElementById("erroToken");
    const btnCancelarToken= document.getElementById("btnCancelarToken");
    const btnConfirmarToken= document.getElementById("btnConfirmarToken");

    let pedidoAtualId = null;

    function abrirModal(idPedido) {
        pedidoAtualId = idPedido;
        campoToken.value = "";
        erroToken.textContent = "";
        overlay.classList.remove("hidden-token");
        campoToken.focus();
    }

    function fecharModal() {
        overlay.classList.add("hidden-token");
        pedidoAtualId = null;
    }

    btnCancelarToken.addEventListener("click", fecharModal);

    btnConfirmarToken.addEventListener("click", async () => {
        const token = campoToken.value.trim();

        if (token === "") {
            erroToken.textContent = "Informe o token.";
            return;
        }

        if (!pedidoAtualId) {
            erroToken.textContent = "Pedido inválido.";
            return;
        }

        try {
            const req = await fetch("php/orders/update_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id: pedidoAtualId,
                    acao: "finalizar",
                    token
                })
            });

            const json = await req.json();

            if (!json.ok) {
                erroToken.textContent = json.msg || "Token inválido.";
                return;
            }

            // Sucesso → fecha modal e recarrega lista
            fecharModal();
            alert("Entrega finalizada com sucesso!");
            buscarPedidos();

        } catch (err) {
            console.error(err);
            erroToken.textContent = "Erro ao finalizar. Tente novamente.";
        }
    });

    // ====== 1) BUSCAR PEDIDOS ======
    async function buscarPedidos() {
        try {
            const req = await fetch("php/orders/get_pending.php");
            const json = await req.json();

            if (!json.ok) {
                lista.innerHTML = `<p class="erro">${json.msg || "Erro ao carregar pedidos."}</p>`;
                return;
            }

            renderPedidos(json.pedidos || []);

        } catch (e) {
            console.error(e);
            lista.innerHTML = `<p class="erro">Falha ao buscar pedidos.</p>`;
        }
    }

    function renderPedidos(pedidos) {
        if (!pedidos.length) {
            lista.innerHTML = `<p>Nenhum pedido pendente no momento.</p>`;
            return;
        }

        lista.innerHTML = pedidos.map(p => {
            let botoes = "";

            if (p.status === "pendente") {
                botoes = `
                    <button class="btn-acao btn-aceitar" data-action="aceitar">Aceitar</button>
                    <button class="btn-acao btn-recusar" data-action="recusar">Recusar</button>
                `;
            } else if (p.status === "aceito") {
                botoes = `
                    <button class="btn-status" data-action="a_caminho">A caminho</button>
                    <button class="btn-status" data-action="finalizar">Finalizar</button>
                `;
            } else if (p.status === "a_caminho") {
                botoes = `
                    <button class="btn-status" data-action="finalizar">Finalizar</button>
                `;
            }

            return `
                <div class="pedido" data-id="${p.id}">
                    <h3>Pedido #${p.id}</h3>
                    <p><strong>Cliente:</strong> ${p.cliente}</p>
                    <p><strong>Endereço:</strong> ${p.endereco}</p>
                    ${botoes}
                </div>
            `;
        }).join("");

        document.querySelectorAll("[data-action]").forEach(btn => {
            btn.addEventListener("click", acaoPedido);
        });
    }

    // ====== 2) AÇÃO DOS BOTÕES ======
    async function acaoPedido(e) {
        const card = e.target.closest(".pedido");
        const id   = card ? card.dataset.id : null;
        const acao = e.target.dataset.action;

        if (!id) {
            alert("ID ausente.");
            return;
        }

        if (acao === "finalizar") {
            // Não chama PHP agora → abre modal de token
            abrirModal(id);
            return;
        }

        // aceitar / recusar / a_caminho
        try {
            const req = await fetch("php/orders/update_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, acao })
            });

            const json = await req.json();

            if (!json.ok) {
                alert(json.msg || "Erro ao atualizar pedido.");
                return;
            }

            if (acao === "recusar") {
                // some da lista
                card.remove();
            } else {
                // recarrega lista para refletir novo status
                buscarPedidos();
            }

        } catch (err) {
            console.error(err);
            alert("Erro ao comunicar com o servidor.");
        }
    }

    // ====== 3) LOOP ======
    buscarPedidos();
    setInterval(buscarPedidos, 5000);
});
