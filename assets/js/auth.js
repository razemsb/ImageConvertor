document.addEventListener('DOMContentLoaded', () => {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
  
    function switchTab(tab) {
      if (tab === 'login') {
        loginForm.classList.replace('hidden-form', 'visible-form');
        registerForm.classList.replace('visible-form', 'hidden-form');
      } else {
        registerForm.classList.replace('hidden-form', 'visible-form');
        loginForm.classList.replace('visible-form', 'hidden-form');
      }
  
      tabBtns.forEach(btn => {
        btn.classList.remove('border-indigo-600', 'border-pink-600', 'text-indigo-600', 'text-pink-600');
        if (btn.dataset.target === tab) {
          btn.classList.add(tab === 'login' ? 'border-indigo-600' : 'border-pink-600');
          btn.classList.add(tab === 'login' ? 'text-indigo-600' : 'text-pink-600');
        }
      });
    }
  
    tabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        switchTab(btn.dataset.target);
      });
    });

    switchTab('login'); 
  
    // Показывать/скрывать пароли
    document.querySelectorAll('.togglePassword').forEach(btn => {
      btn.addEventListener('click', e => {
        const input = e.target.closest('button').previousElementSibling;
        if (!input) return;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        btn.innerHTML = type === 'password' ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
      });
    });
  });
