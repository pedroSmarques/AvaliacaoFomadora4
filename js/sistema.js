// js/sistema.js
document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // ELEMENTOS
    // ============================
    const listaProdutos   = document.getElementById("listaProdutos");
    const itensCarrinho   = document.getElementById("itensCarrinho");
    const totalCarrinho   = document.getElementById("totalCarrinho");
    const btnFinalizar    = document.getElementById("btnFinalizar");

    const cep             = document.getElementById("cep");
    const rua             = document.getElementById("rua");
    const numero          = document.getElementById("numero");
    const complemento     = document.getElementById("complemento");
    const btnSalvar       = document.getElementById("btnSalvarEndereco");

    const loading         = document.getElementById("loading");

    let enderecoSalvo = null;
    let carrinho      = [];

    // ============================
    // 1) LISTAR PRODUTOS
    // ============================
    async function carregarProdutos() {
        try {
            const r = await fetch("php/orders/get_produtos.php");
            const produtos = await r.json();

            listaProdutos.innerHTML = "";

            produtos.forEach(p => {
                const btn = document.createElement("button");
                btn.className     = "btn-produto";
                btn.dataset.id    = p.id;
                btn.dataset.nome  = p.nome;
                btn.dataset.preco = p.preco;
                btn.textContent   = `${p.nome} — R$ ${Number(p.preco).toFixed(2)}`;

                btn.addEventListener("click", () => {
                    carrinho.push({
                        id:    p.id,
                        nome:  p.nome,
                        preco: Number(p.preco)
                    });
                    atualizarCarrinho();
                });

                listaProdutos.appendChild(btn);
            });
        } catch (e) {
            alert("Erro ao carregar produtos.");
            console.error(e);
        }
    }

    // ============================
    // 2) CARRINHO
    // ============================
    function atualizarCarrinho() {
        itensCarrinho.innerHTML = carrinho.map((item, i) => `
            <div class="item">
                ${item.nome} — R$ ${item.preco.toFixed(2)}
                <button class="remover" data-i="${i}">x</button>
            </div>
        `).join("");

        document.querySelectorAll(".remover").forEach(btn => {
            btn.onclick = () => {
                carrinho.splice(btn.dataset.i, 1);
                atualizarCarrinho();
            };
        });

        const total = carrinho.reduce((t, x) => t + x.preco, 0);
        totalCarrinho.innerHTML = `<h3>Total: R$ ${total.toFixed(2)}</h3>`;
    }

    // ============================
    // 3) CEP + VIA CEP
    // ============================
    cep.addEventListener("input", () => {
        let v = cep.value.replace(/\D/g, "");
        if (v.length > 5) v = v.replace(/(\d{5})(\d)/, "$1-$2");
        cep.value = v;
    });

    cep.addEventListener("blur", async () => {
        const valor = cep.value.replace(/\D/g, "");
        if (valor.length !== 8) return;

        try {
            const r = await fetch(`https://viacep.com.br/ws/${valor}/json/`);
            const dados = await r.json();
            if (!dados.erro) rua.value = dados.logradouro;
        } catch (e) {
            console.error("Erro ViaCEP", e);
        }
    });

    // ============================
    // 4) SALVAR ENDEREÇO (só em memória)
    // ============================
    btnSalvar.addEventListener("click", () => {

        if (!cep.value || !rua.value || !numero.value) {
            alert("Preencha CEP, rua e número.");
            return;
        }

        enderecoSalvo = {
            cep:         cep.value,
            rua:         rua.value,
            numero:      numero.value,
            complemento: complemento.value
        };

        alert("Endereço salvo!");
    });

    // ============================
    // 5) FINALIZAR PEDIDO
    // ============================
    btnFinalizar.addEventListener("click", async () => {

        if (!enderecoSalvo) {
            alert("Salve o endereço antes.");
            return;
        }

        if (carrinho.length === 0) {
            alert("Carrinho vazio!");
            return;
        }

        loading.classList.remove("hidden");

        try {
            const resp = await fetch("php/orders/criar_pedido.php", {
                method:  "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    itens:    carrinho,
                    endereco: enderecoSalvo
                })
            });

            const texto = await resp.text();
            let data;

            try {
                data = JSON.parse(texto);
            } catch (e) {
                console.error("Resposta não é JSON:", texto);
                alert("Resposta inválida do servidor.");
                loading.classList.add("hidden");
                return;
            }

            loading.classList.add("hidden");

            if (!data.ok) {
                alert(data.mensagem || "Erro ao finalizar o pedido.");
                return;
            }

            // SUCESSO
            alert(`Pedido criado! Seu token é: ${data.token}`);
            carrinho = [];
            atualizarCarrinho();

        } catch (err) {
            loading.classList.add("hidden");
            alert("Erro inesperado. Tente novamente.");
            console.error(err);
        }
    });

    // ============================
    // INICIAR SISTEMA
    // ============================
    carregarProdutos();

});
