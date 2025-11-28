// js/admin.js
// Painel Admin: troca de seções, CRUD de produtos, clientes, entregadores, pedidos e logs

document.addEventListener("DOMContentLoaded", () => {

    // ---------------- MENU LATERAL / SEÇÕES ----------------
    const botoesMenu = document.querySelectorAll(".item-menu");
    const secoes = document.querySelectorAll(".secao");

    botoesMenu.forEach(botao => {
        botao.addEventListener("click", () => {
            botoesMenu.forEach(b => b.classList.remove("ativo"));
            botao.classList.add("ativo");

            const alvo = botao.getAttribute("data-alvo");

            secoes.forEach(sec => {
                if (sec.id === alvo) {
                    sec.classList.add("visivel");
                } else {
                    sec.classList.remove("visivel");
                }
            });
        });
    });

    // =======================================================
    // 1) PRODUTOS
    // =======================================================
    const formProduto = document.getElementById("formProduto");
    const produtoIdHidden = document.getElementById("produto_id");
    const nomeProdutoInput = document.getElementById("nome_produto");
    const precoProdutoInput = document.getElementById("preco_produto");
    const msgProdutos = document.getElementById("msgProdutos");
    const tbodyProdutos = document.getElementById("tbodyProdutos");
    const btnCancelarEdicao = document.getElementById("btnCancelarEdicao");
    const tituloFormProduto = document.getElementById("tituloFormProduto");

    const erroNomeProduto = document.getElementById("erroNomeProduto");
    const erroPrecoProduto = document.getElementById("erroPrecoProduto");

    function limparErrosProduto() {
        erroNomeProduto.textContent = "";
        erroPrecoProduto.textContent = "";
        msgProdutos.textContent = "";
        msgProdutos.className = "mensagem";
    }

    async function carregarProdutos() {
        try {
            const resposta = await fetch("php/admin/produtos/listar_produtos.php");
            const data = await resposta.json();

            if (!data.ok) {
                msgProdutos.textContent = data.mensagem || "Erro ao carregar produtos.";
                msgProdutos.classList.add("erro");
                return;
            }

            const produtos = data.produtos;
            tbodyProdutos.innerHTML = "";

            if (!produtos.length) {
                msgProdutos.textContent = "Nenhum produto cadastrado.";
                return;
            }

            produtos.forEach(prod => {
                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <td>${prod.id}</td>
                    <td>${escapeHtml(prod.nome)}</td>
                    <td>${formatarPreco(prod.preco)}</td>
                    <td>${prod.ativo == 1 ? "Sim" : "Não"}</td>
                    <td class="acoes">
                        <button class="btn-preto btn-sm" data-acao="editar-produto" data-id="${prod.id}">Editar</button>
                        <button class="btn-vermelho btn-sm" data-acao="excluir-produto" data-id="${prod.id}">Excluir</button>
                    </td>
                `;

                tbodyProdutos.appendChild(tr);
            });

            tbodyProdutos.querySelectorAll("button").forEach(btn => {
                btn.addEventListener("click", onAcaoProduto);
            });

            const numProdutos = document.getElementById("numProdutos");
            if (numProdutos) {
                numProdutos.textContent = produtos.length;
            }

        } catch (e) {
            console.error("Erro carregarProdutos:", e);
            msgProdutos.textContent = "Erro inesperado ao carregar produtos.";
            msgProdutos.classList.add("erro");
        }
    }

    function onAcaoProduto(e) {
        const btn = e.currentTarget;
        const id = btn.getAttribute("data-id");
        const acao = btn.getAttribute("data-acao");

        if (acao === "editar-produto") {
            editarProduto(id);
        } else if (acao === "excluir-produto") {
            if (confirm("Deseja realmente excluir este produto?")) {
                excluirProduto(id);
            }
        }
    }

    async function editarProduto(id) {
        limparErrosProduto();
        try {
            const resp = await fetch(`php/admin/produtos/listar_produtos.php?id=${encodeURIComponent(id)}`);
            const data = await resp.json();
            if (!data.ok || !data.produto) {
                msgProdutos.textContent = data.mensagem || "Produto não encontrado.";
                msgProdutos.classList.add("erro");
                return;
            }
            const p = data.produto;
            produtoIdHidden.value = p.id;
            nomeProdutoInput.value = p.nome;
            precoProdutoInput.value = String(p.preco).replace(".", ",");

            tituloFormProduto.textContent = "Editar Produto";
            btnCancelarEdicao.style.display = "inline-block";
        } catch (e) {
            console.error("Erro editarProduto:", e);
            msgProdutos.textContent = "Erro ao buscar produto.";
            msgProdutos.classList.add("erro");
        }
    }

    async function excluirProduto(id) {
        try {
            const resp = await fetch("php/admin/produtos/deletar_produto.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            });
            const data = await resp.json();
            if (!data.ok) {
                msgProdutos.textContent = data.mensagem || "Erro ao excluir.";
                msgProdutos.classList.add("erro");
                return;
            }
            msgProdutos.textContent = "Produto excluído com sucesso.";
            msgProdutos.classList.add("sucesso");
            carregarProdutos();
            resetarFormProduto();
        } catch (e) {
            console.error("Erro excluirProduto:", e);
            msgProdutos.textContent = "Erro inesperado ao excluir produto.";
            msgProdutos.classList.add("erro");
        }
    }

    formProduto.addEventListener("submit", async (e) => {
        e.preventDefault();
        limparErrosProduto();

        const id = produtoIdHidden.value.trim();
        const nome = nomeProdutoInput.value.trim();
        let preco = precoProdutoInput.value.trim();

        let houveErro = false;

        if (nome.length < 2) {
            erroNomeProduto.textContent = "Informe um nome válido.";
            houveErro = true;
        }

        if (!preco) {
            erroPrecoProduto.textContent = "Informe o preço.";
            houveErro = true;
        } else {
            preco = preco.replace(".", "").replace(",", ".");
            const n = Number(preco);
            if (isNaN(n) || n <= 0) {
                erroPrecoProduto.textContent = "Preço inválido.";
                houveErro = true;
            }
        }

        if (houveErro) return;

        try {
            const resp = await fetch("php/admin/produtos/salvar_produto.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, nome, preco })
            });

            const data = await resp.json();

            if (!data.ok) {
                msgProdutos.textContent = data.mensagem || "Erro ao salvar produto.";
                msgProdutos.classList.add("erro");
                return;
            }

            msgProdutos.textContent = data.mensagem || "Produto salvo com sucesso.";
            msgProdutos.classList.add("sucesso");

            resetarFormProduto();
            carregarProdutos();

        } catch (e) {
            console.error("Erro salvar produto:", e);
            msgProdutos.textContent = "Erro inesperado ao salvar.";
            msgProdutos.classList.add("erro");
        }
    });

    btnCancelarEdicao.addEventListener("click", () => {
        resetarFormProduto();
    });

    function resetarFormProduto() {
        produtoIdHidden.value = "";
        nomeProdutoInput.value = "";
        precoProdutoInput.value = "";
        tituloFormProduto.textContent = "Adicionar Produto";
        btnCancelarEdicao.style.display = "none";
        limparErrosProduto();
    }

    // =======================================================
    // 2) CLIENTES
    // =======================================================
    const tbodyClientes = document.getElementById("tbodyClientes");
    const msgClientes = document.getElementById("msgClientes");
    const filtroClienteCpf = document.getElementById("filtroClienteCpf");
    const filtroClienteNome = document.getElementById("filtroClienteNome");
    const filtroClienteEmail = document.getElementById("filtroClienteEmail");
    const btnFiltrarClientes = document.getElementById("btnFiltrarClientes");
    const btnLimparFiltroClientes = document.getElementById("btnLimparFiltroClientes");

    const cardEditarCliente = document.getElementById("cardEditarCliente");
    const formEditarCliente = document.getElementById("formEditarCliente");
    const idClienteEditar = document.getElementById("id_cliente_editar");
    const nomeClienteEditar = document.getElementById("nome_cliente_editar");
    const emailClienteEditar = document.getElementById("email_cliente_editar");
    const cpfClienteEditar = document.getElementById("cpf_cliente_editar");
    const btnCancelarClienteEdicao = document.getElementById("btnCancelarClienteEdicao");

    async function carregarClientes() {
        if (!tbodyClientes) return;
        try {
            const params = new URLSearchParams();
            params.append("role", "cliente");

            const cpf = filtroClienteCpf?.value.replace(/\D/g, "") || "";
            const nome = filtroClienteNome?.value.trim() || "";
            const email = filtroClienteEmail?.value.trim() || "";

            if (cpf) params.append("cpf", cpf);
            if (nome) params.append("nome", nome);
            if (email) params.append("email", email);

            const resp = await fetch("php/admin/usuarios/listar_usuarios.php?" + params.toString());
            const data = await resp.json();

            if (!data.ok) {
                msgClientes.textContent = data.mensagem || "Erro ao carregar clientes.";
                msgClientes.classList.add("erro");
                return;
            }

            const clientes = data.usuarios || [];
            tbodyClientes.innerHTML = "";
            msgClientes.textContent = "";
            msgClientes.className = "mensagem";

            if (!clientes.length) {
                msgClientes.textContent = "Nenhum cliente encontrado.";
                return;
            }

            clientes.forEach(u => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${u.id}</td>
                    <td>${escapeHtml(u.nome_completo)}</td>
                    <td>${escapeHtml(u.email)}</td>
                    <td>${u.cpf || ""}</td>
                    <td class="acoes">
                        <button class="btn-preto btn-sm" data-acao="editar-usuario" data-tipo="cliente" data-id="${u.id}">Editar</button>
                        <button class="btn-vermelho btn-sm" data-acao="excluir-usuario" data-tipo="cliente" data-id="${u.id}">Excluir</button>
                    </td>
                `;
                tbodyClientes.appendChild(tr);
            });

            tbodyClientes.querySelectorAll("button").forEach(btn => {
                btn.addEventListener("click", onAcaoUsuario);
            });

            const numUsuarios = document.getElementById("numUsuarios");
            if (numUsuarios) numUsuarios.textContent = clientes.length;

        } catch (e) {
            console.error("Erro carregarClientes:", e);
            msgClientes.textContent = "Erro inesperado ao carregar clientes.";
            msgClientes.classList.add("erro");
        }
    }

    if (btnFiltrarClientes) {
        btnFiltrarClientes.addEventListener("click", () => {
            carregarClientes();
        });
    }

    if (btnLimparFiltroClientes) {
        btnLimparFiltroClientes.addEventListener("click", () => {
            if (filtroClienteCpf) filtroClienteCpf.value = "";
            if (filtroClienteNome) filtroClienteNome.value = "";
            if (filtroClienteEmail) filtroClienteEmail.value = "";
            carregarClientes();
        });
    }

    // =======================================================
    // 3) ENTREGADORES
    // =======================================================
    const tbodyEntregadores = document.getElementById("tbodyEntregadores");
    const msgEntregadores = document.getElementById("msgEntregadores");
    const filtroEntNome = document.getElementById("filtroEntNome");
    const filtroEntEmail = document.getElementById("filtroEntEmail");
    const btnFiltrarEntregadores = document.getElementById("btnFiltrarEntregadores");
    const btnLimparFiltroEntregadores = document.getElementById("btnLimparFiltroEntregadores");

    const cardEditarEntregador = document.getElementById("cardEditarEntregador");
    const formEditarEntregador = document.getElementById("formEditarEntregador");
    const idEntregadorEditar = document.getElementById("id_entregador_editar");
    const nomeEntregadorEditar = document.getElementById("nome_entregador_editar");
    const emailEntregadorEditar = document.getElementById("email_entregador_editar");
    const veiculoEntregadorEditar = document.getElementById("veiculo_entregador_editar");
    const btnCancelarEntregadorEdicao = document.getElementById("btnCancelarEntregadorEdicao");

    async function carregarEntregadores() {
        if (!tbodyEntregadores) return;
        try {
            const params = new URLSearchParams();
            params.append("role", "entregador");

            const nome = filtroEntNome?.value.trim() || "";
            const email = filtroEntEmail?.value.trim() || "";

            if (nome) params.append("nome", nome);
            if (email) params.append("email", email);

            const resp = await fetch("php/admin/usuarios/listar_usuarios.php?" + params.toString());
            const data = await resp.json();

            if (!data.ok) {
                msgEntregadores.textContent = data.mensagem || "Erro ao carregar entregadores.";
                msgEntregadores.classList.add("erro");
                return;
            }

            const entregadores = data.usuarios || [];
            tbodyEntregadores.innerHTML = "";
            msgEntregadores.textContent = "";
            msgEntregadores.className = "mensagem";

            if (!entregadores.length) {
                msgEntregadores.textContent = "Nenhum entregador encontrado.";
                return;
            }

            entregadores.forEach(u => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${u.id}</td>
                    <td>${escapeHtml(u.nome_completo)}</td>
                    <td>${escapeHtml(u.email)}</td>
                    <td>${u.veiculo || ""}</td>
                    <td class="acoes">
                        <button class="btn-preto btn-sm" data-acao="editar-usuario" data-tipo="entregador" data-id="${u.id}">Editar</button>
                        <button class="btn-vermelho btn-sm" data-acao="excluir-usuario" data-tipo="entregador" data-id="${u.id}">Excluir</button>
                    </td>
                `;
                tbodyEntregadores.appendChild(tr);
            });

            tbodyEntregadores.querySelectorAll("button").forEach(btn => {
                btn.addEventListener("click", onAcaoUsuario);
            });

            const numEnt = document.getElementById("numEntregadores");
            if (numEnt) numEnt.textContent = entregadores.length;

        } catch (e) {
            console.error("Erro carregarEntregadores:", e);
            msgEntregadores.textContent = "Erro inesperado ao carregar entregadores.";
            msgEntregadores.classList.add("erro");
        }
    }

    if (btnFiltrarEntregadores) {
        btnFiltrarEntregadores.addEventListener("click", () => {
            carregarEntregadores();
        });
    }

    if (btnLimparFiltroEntregadores) {
        btnLimparFiltroEntregadores.addEventListener("click", () => {
            if (filtroEntNome) filtroEntNome.value = "";
            if (filtroEntEmail) filtroEntEmail.value = "";
            carregarEntregadores();
        });
    }

    // =======================================================
    // 3.1) AÇÕES USUÁRIOS (editar / excluir)
    // =======================================================
    async function onAcaoUsuario(e) {
        const btn = e.currentTarget;
        const id = btn.getAttribute("data-id");
        const acao = btn.getAttribute("data-acao");
        const tipo = btn.getAttribute("data-tipo"); // cliente ou entregador

        if (acao === "excluir-usuario") {
            if (!confirm("Deseja realmente excluir este usuário?")) return;

            try {
                const resp = await fetch("php/admin/usuarios/deletar_usuario.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id })
                });
                const data = await resp.json();
                if (!data.ok) {
                    alert(data.mensagem || "Erro ao excluir usuário.");
                    return;
                }
                alert("Usuário excluído com sucesso.");
                carregarClientes();
                carregarEntregadores();
            } catch (e2) {
                console.error("Erro excluir usuario:", e2);
                alert("Erro inesperado ao excluir usuário.");
            }
        }

        if (acao === "editar-usuario") {
            carregarUsuarioParaEdicao(id, tipo);
        }
    }

    async function carregarUsuarioParaEdicao(id, tipo) {
        try {
            const resp = await fetch("php/admin/usuarios/listar_usuarios.php?id=" + encodeURIComponent(id));
            const data = await resp.json();

            if (!data.ok || !data.usuario) {
                alert(data.mensagem || "Usuário não encontrado.");
                return;
            }

            const u = data.usuario;

            if (tipo === "cliente") {
                idClienteEditar.value = u.id;
                nomeClienteEditar.value = u.nome_completo || "";
                emailClienteEditar.value = u.email || "";
                cpfClienteEditar.value = u.cpf || "";
                cardEditarCliente.style.display = "block";
            } else {
                idEntregadorEditar.value = u.id;
                nomeEntregadorEditar.value = u.nome_completo || "";
                emailEntregadorEditar.value = u.email || "";
                if (veiculoEntregadorEditar) veiculoEntregadorEditar.value = u.veiculo || "";
                cardEditarEntregador.style.display = "block";
            }

        } catch (e) {
            console.error("Erro carregarUsuarioParaEdicao:", e);
            alert("Erro ao carregar dados do usuário.");
        }
    }

    // Salvar edição cliente
    if (formEditarCliente) {
        formEditarCliente.addEventListener("submit", async (e) => {
            e.preventDefault();

            const id = idClienteEditar.value;
            const nome = nomeClienteEditar.value.trim();
            const email = emailClienteEditar.value.trim();
            const cpf = cpfClienteEditar.value.replace(/\D/g, "");

            if (!nome || !email) {
                alert("Nome e e-mail são obrigatórios.");
                return;
            }

            try {
                const resp = await fetch("php/admin/usuarios/atualizar_usuario.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id,
                        role: "cliente",
                        nome,
                        email,
                        cpf
                    })
                });

                const data = await resp.json();
                if (!data.ok) {
                    alert(data.mensagem || "Erro ao atualizar cliente.");
                    return;
                }

                alert("Cliente atualizado com sucesso.");
                cardEditarCliente.style.display = "none";
                carregarClientes();

            } catch (e2) {
                console.error("Erro atualizar cliente:", e2);
                alert("Erro inesperado ao atualizar cliente.");
            }
        });
    }

    if (btnCancelarClienteEdicao) {
        btnCancelarClienteEdicao.addEventListener("click", () => {
            cardEditarCliente.style.display = "none";
        });
    }

    // Salvar edição entregador
    if (formEditarEntregador) {
        formEditarEntregador.addEventListener("submit", async (e) => {
            e.preventDefault();

            const id = idEntregadorEditar.value;
            const nome = nomeEntregadorEditar.value.trim();
            const email = emailEntregadorEditar.value.trim();
            const veiculo = veiculoEntregadorEditar.value;

            if (!nome || !email) {
                alert("Nome e e-mail são obrigatórios.");
                return;
            }

            try {
                const resp = await fetch("php/admin/usuarios/atualizar_usuario.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        id,
                        role: "entregador",
                        nome,
                        email,
                        veiculo
                    })
                });

                const data = await resp.json();
                if (!data.ok) {
                    alert(data.mensagem || "Erro ao atualizar entregador.");
                    return;
                }

                alert("Entregador atualizado com sucesso.");
                cardEditarEntregador.style.display = "none";
                carregarEntregadores();

            } catch (e2) {
                console.error("Erro atualizar entregador:", e2);
                alert("Erro inesperado ao atualizar entregador.");
            }
        });
    }

    if (btnCancelarEntregadorEdicao) {
        btnCancelarEntregadorEdicao.addEventListener("click", () => {
            cardEditarEntregador.style.display = "none";
        });
    }

    // =======================================================
    // 4) PEDIDOS
    // =======================================================
    const tbodyPedidos = document.getElementById("tbodyPedidos");
    const msgPedidos = document.getElementById("msgPedidos");

    async function carregarPedidos() {
        if (!tbodyPedidos) return;
        try {
            const resp = await fetch("php/admin/pedidos/listar_pedidos.php");
            const data = await resp.json();

            if (!data.ok) {
                msgPedidos.textContent = data.mensagem || "Erro ao carregar pedidos.";
                msgPedidos.classList.add("erro");
                return;
            }

            const pedidos = data.pedidos || [];
            tbodyPedidos.innerHTML = "";
            msgPedidos.textContent = "";
            msgPedidos.className = "mensagem";

            if (!pedidos.length) {
                msgPedidos.textContent = "Nenhum pedido encontrado.";
                return;
            }

            pedidos.forEach(p => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${p.id}</td>
                    <td>${escapeHtml(p.cliente)}</td>
                    <td>${escapeHtml(p.endereco)}</td>
                    <td>${p.status}</td>
                    <td>${p.created_at}</td>
                `;
                tbodyPedidos.appendChild(tr);
            });

            const numPedidos = document.getElementById("numPedidos");
            if (numPedidos) numPedidos.textContent = pedidos.length;

        } catch (e) {
            console.error("Erro carregarPedidos:", e);
            msgPedidos.textContent = "Erro inesperado ao carregar pedidos.";
            msgPedidos.classList.add("erro");
        }
    }

    // =======================================================
    // 5) LOGS DE ACESSO
    // =======================================================
    const tbodyLogs = document.getElementById("tbodyLogs");
    const msgLogs = document.getElementById("msgLogs");

    async function carregarLogs() {
        if (!tbodyLogs) return;
        try {
            const resp = await fetch("php/admin/logs/listar_logs.php");
            const data = await resp.json();

            if (!data.ok) {
                msgLogs.textContent = data.mensagem || "Erro ao carregar logs.";
                msgLogs.classList.add("erro");
                return;
            }

            const logs = data.logs || [];
            tbodyLogs.innerHTML = "";
            msgLogs.textContent = "";
            msgLogs.className = "mensagem";

            if (!logs.length) {
                msgLogs.textContent = "Nenhum acesso registrado.";
                return;
            }

            logs.forEach(l => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${l.id}</td>
                    <td>${escapeHtml(l.usuario)}</td>
                    <td>${l.role}</td>
                    <td>${l.data_hora}</td>
                `;
                tbodyLogs.appendChild(tr);
            });

        } catch (e) {
            console.error("Erro carregarLogs:", e);
            msgLogs.textContent = "Erro inesperado ao carregar logs.";
            msgLogs.classList.add("erro");
        }
    }

    // =======================================================
    // 6) RESUMO (cards do dashboard)
    // =======================================================
    async function carregarResumo() {
        try {
            const resp = await fetch("php/admin/resumo.php");
            const data = await resp.json();
            if (!data.ok) return;

            const { total_produtos, total_clientes, total_entregadores, total_pedidos } = data;

            const nProd = document.getElementById("numProdutos");
            const nCli = document.getElementById("numUsuarios");
            const nEnt = document.getElementById("numEntregadores");
            const nPed = document.getElementById("numPedidos");

            if (nProd && typeof total_produtos !== "undefined") nProd.textContent = total_produtos;
            if (nCli && typeof total_clientes !== "undefined") nCli.textContent = total_clientes;
            if (nEnt && typeof total_entregadores !== "undefined") nEnt.textContent = total_entregadores;
            if (nPed && typeof total_pedidos !== "undefined") nPed.textContent = total_pedidos;

        } catch (e) {
            console.error("Erro carregarResumo:", e);
        }
    }

    // =======================================================
    // Funções utilitárias
    // =======================================================
    function escapeHtml(str) {
        if (!str) return "";
        return String(str).replace(/[&<>"']/g, (m) => {
            const map = { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" };
            return map[m];
        });
    }

    function formatarPreco(valor) {
        const n = Number(valor);
        if (isNaN(n)) return valor;
        return n.toFixed(2).replace(".", ",");
    }

    // =======================================================
    // INICIALIZAÇÃO
    // =======================================================
    carregarProdutos();
    carregarClientes();
    carregarEntregadores();
    carregarPedidos();
    carregarLogs();
    carregarResumo();

});
