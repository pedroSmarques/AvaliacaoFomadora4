
// Validação simples de campos vazios no login

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("formLogin");
  const email = document.getElementById("email");
  const senha = document.getElementById("senha");
  const erroEmail = document.getElementById("erroEmail");
  const erroSenha = document.getElementById("erroSenha");

  form.addEventListener("submit", (e) => {
    // limpa mensagens
    erroEmail.textContent = "";
    erroSenha.textContent = "";

    let temErro = false;

    if (email.value.trim() === "") {
      erroEmail.textContent = "Preencha campo vazio.";
      temErro = true;
    }

    if (senha.value.trim() === "") {
      erroSenha.textContent = "Preencha campo vazio.";
      temErro = true;
    }

    if (temErro) {
      e.preventDefault();
      return;
    }


  });
});
