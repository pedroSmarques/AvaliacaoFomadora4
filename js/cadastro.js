// js/cadastro.js CORRIGIDO COMPLETO

document.addEventListener("DOMContentLoaded", () => {

    // CAMPOS BÁSICOS -----------------------------
    const tipo = document.getElementById("tipo_usuario");   // <-- CORRIGIDO
    const camposEntregador = document.getElementById("camposEntregador");
    const veiculo = document.getElementById("veiculo");
    const camposVeiculoMotorizado = document.getElementById("camposVeiculoMotorizado");

    const form = document.getElementById("formCadastro");

    const nome = document.getElementById("nome");
    const cpf = document.getElementById("cpf");
    const rg = document.getElementById("rg");
    const dataN = document.getElementById("data_nascimento");
    const email = document.getElementById("email");
    const senha = document.getElementById("senha");
    const confirmar = document.getElementById("confirmar_senha");
    const cep = document.getElementById("cep");
    const endereco = document.getElementById("endereco");
    const numero = document.getElementById("numero");
    const complemento = document.getElementById("complemento");
    const modelo = document.getElementById("modelo");
    const placa = document.getElementById("placa");

    // LIMPA ERROS --------------------------------
    function limparErros() {
        document.querySelectorAll(".erro-campo").forEach(x => x.textContent = "");
    }

    // 1️⃣ MOSTRAR CAMPOS DO ENTREGADOR
    tipo.addEventListener("change", () => {
        if (tipo.value === "entregador") {
            camposEntregador.classList.remove("hidden");
        } else {
            camposEntregador.classList.add("hidden");
        }
    });

    // 2️⃣ CAMPOS PARA MOTO / CARRO
    veiculo.addEventListener("change", () => {
        if (veiculo.value === "moto" || veiculo.value === "carro") {
            camposVeiculoMotorizado.classList.remove("hidden");
        } else {
            camposVeiculoMotorizado.classList.add("hidden");
        }
    });

    // 3️⃣ MÁSCARA CPF
    cpf.addEventListener("input", () => {
        let v = cpf.value.replace(/\D/g, "");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        cpf.value = v;
    });

    // 4️⃣ MÁSCARA CEP
    cep.addEventListener("input", () => {
        let v = cep.value.replace(/\D/g, "");
        if (v.length > 5) v = v.replace(/(\d{5})(\d)/, "$1-$2");
        cep.value = v;
    });

    // 5️⃣ API VIA CEP
    cep.addEventListener("blur", async () => {
        const apenasNum = cep.value.replace(/\D/g, "");

        if (apenasNum.length !== 8) return;

        try {
            const r = await fetch(`https://viacep.com.br/ws/${apenasNum}/json/`);
            const dado = await r.json();

            endereco.value = dado.erro ? "" : dado.logradouro;

        } catch (err) {
            console.log("Erro API:", err);
        }
    });

    // 6️⃣ MÁSCARA PLACA MERCOSUL
    placa.addEventListener("input", () => {
        placa.value = placa.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
    });

    // 7️⃣ VALIDAÇÃO COMPLETA -------------------------
    form.addEventListener("submit", (e) => {

        limparErros();

        let erro = false;

        // Tipo
        if (tipo.value === "") {
            erro = true;
            document.getElementById("erroTipo").textContent = "Selecione o tipo.";
        }

        if (nome.value.trim().length < 3) {
            erro = true;
            erroNome.textContent = "O nome deve conter entre 3 e 60 caracteres.";
        }

        // CPF
        const regexCPF = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        if (!regexCPF.test(cpf.value)) {
            erro = true;
            erroCpf.textContent = "CPF inválido.";
        }

        // Email
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(email.value)) {
            erro = true;
            erroEmail.textContent = "Email inválido.";
        }

        // Senhas
        if (senha.value.length < 8) {
            erro = true;
            erroSenha.textContent = "Senha deve possuir 8 caracteres.";
        }

        if (senha.value !== confirmar.value) {
            erro = true;
            erroConfirmar.textContent = "Senhas não coincidem.";
        }

        // CEP
        if (!/^\d{5}-\d{3}$/.test(cep.value)) {
            erro = true;
            erroCep.textContent = "CEP inválido.";
        }

        // ENTREGADOR ----------------------------------------
        if (tipo.value === "entregador") {

            if (rg.value.trim().length < 5) {
                erro = true;
                erroRg.textContent = "RG inválido.";
            }

            if (veiculo.value === "") {
                erro = true;
                erroVeiculo.textContent = "Selecione o veículo.";
            }

            if (veiculo.value === "moto" || veiculo.value === "carro") {

                if (modelo.value.trim().length < 2) {
                    erro = true;
                    erroModelo.textContent = "Modelo inválido.";
                }

                const regexPlaca = /^[A-Z]{3}[0-9][A-Z][0-9]{2}$/;
                if (!regexPlaca.test(placa.value.trim())) {
                    erro = true;
                    erroPlaca.textContent = "Placa inválida (Mercosul).";
                }

            }

            // Maior de idade
            if (calcularIdade(dataN.value) < 18) {
                erro = true;
                erroData.textContent = "Você deve ser maior de idade.";
            }
        }

        // BLOQUEIA ENVIO
        if (erro) {
            e.preventDefault();
            return;
        }
    });

    // FUNÇÃO IDADE
    function calcularIdade(data) {
        const nasc = new Date(data);
        const hoje = new Date();
        let idade = hoje.getFullYear() - nasc.getFullYear();
        const m = hoje.getMonth() - nasc.getMonth();
        if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) idade--;
        return idade;
    }

});
