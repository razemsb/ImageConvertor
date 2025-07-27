document.addEventListener('DOMContentLoaded', function () {

  const urlParams = new URLSearchParams(window.location.search);
  const actionParam = urlParams.get('action');

  if (actionParam === 'register') {
    switchToTab('register');
  }

  const tabButtons = document.querySelectorAll('.tab-btn');
  tabButtons.forEach(button => {
    button.addEventListener('click', function () {
      const target = this.dataset.target;
      switchToTab(target);

      const newUrl = new URL(window.location);
      newUrl.searchParams.set('action', target);
      window.history.pushState({}, '', newUrl);
    });
  });

  function switchToTab(target) {
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.target === target);
    });

    document.querySelectorAll('.auth-form').forEach(form => {
      form.classList.toggle('active', form.id === `${target}Form`);
    });
  }


  document.querySelectorAll('.togglePassword').forEach(button => {
    button.addEventListener('click', function () {
      const input = this.parentElement.querySelector('input');
      const icon = this.querySelector('i');

      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    });
  });

  function handleEnterKey(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const form = e.target.form;
      const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"])'));
      const currentIndex = inputs.indexOf(e.target);

      if (currentIndex < inputs.length - 1) {
        inputs[currentIndex + 1].focus();
      } else {
        form.querySelector('button[type="submit"]').focus();
      }
    }
  }


  document.querySelectorAll('input').forEach(input => {
    input.addEventListener('keydown', handleEnterKey);
  });


  const passwordInput = document.getElementById('registerPassword');
  const passwordConfirm = document.getElementById('registerPasswordConfirm');
  const passwordStrengthBar = document.querySelector('.password-strength-bar');
  const passwordRequirements = document.querySelectorAll('.password-requirement');
  const passwordMatch = document.querySelector('.password-match');
  const registerSubmit = document.getElementById('registerSubmit');

  if (passwordInput) {
    passwordInput.addEventListener('input', validatePassword);
    passwordConfirm.addEventListener('input', validatePasswordConfirmation);
  }

  function validatePassword() {
    const password = this.value;
    let strength = 0;


    if (password.length >= 8) strength += 25;


    if (/[A-Z]/.test(password)) strength += 25;


    if (/[0-9]/.test(password)) strength += 25;


    if (/[^A-Za-z0-9]/.test(password)) strength += 25;


    passwordStrengthBar.style.width = `${strength}%`;


    if (strength < 50) {
      passwordStrengthBar.style.backgroundColor = '#ef4444';
    } else if (strength < 75) {
      passwordStrengthBar.style.backgroundColor = '#f59e0b';
    } else {
      passwordStrengthBar.style.backgroundColor = '#10b981';
    }


    checkRequirement('length', password.length >= 8);
    checkRequirement('uppercase', /[A-Z]/.test(password));
    checkRequirement('number', /[0-9]/.test(password));
    checkRequirement('special', /[^A-Za-z0-9]/.test(password));


    validatePasswordConfirmation();
  }

  function checkRequirement(type, isMet) {
    const requirement = document.querySelector(`.password-requirement[data-requirement="${type}"]`);
    if (requirement) {
      const icon = requirement.querySelector('i');
      if (isMet) {
        icon.classList.remove('fa-circle', 'text-gray-400');
        icon.classList.add('fa-check-circle', 'text-green-500');
        requirement.classList.add('requirement-met');
        requirement.classList.remove('requirement-not-met');
      } else {
        icon.classList.remove('fa-check-circle', 'text-green-500');
        icon.classList.add('fa-circle', 'text-gray-400');
        requirement.classList.add('requirement-not-met');
        requirement.classList.remove('requirement-met');
      }
    }
  }

  function validatePasswordConfirmation() {
    const password = passwordInput.value;
    const confirmPassword = passwordConfirm.value;

    if (confirmPassword.length > 0 && password === confirmPassword) {
      passwordMatch.classList.remove('hidden');
      registerSubmit.disabled = false;
      registerSubmit.classList.remove('opacity-70', 'cursor-not-allowed');
    } else {
      passwordMatch.classList.add('hidden');
      registerSubmit.disabled = true;
      registerSubmit.classList.add('opacity-70', 'cursor-not-allowed');
    }
  }
});