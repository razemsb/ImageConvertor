let cropper;
let currentImage;

// Активация настроек обрезки
document.getElementById('enableCrop').addEventListener('change', function(e) {
  document.getElementById('cropSettings').classList.toggle('hidden', !e.target.checked);
});

// Открытие модального окна для обрезки
function openCropModal(imageElement) {
  currentImage = imageElement;
  const modal = document.getElementById('cropModal');
  const img = document.getElementById('cropImage');
  
  img.src = imageElement.src;
  modal.classList.remove('hidden');
  
  // Инициализация Cropper.js
  cropper = new Cropper(img, {
    aspectRatio: NaN, // По умолчанию - произвольные
    viewMode: 1,
    autoCropArea: 0.8,
    responsive: true
  });
}

// Закрытие модального окна
document.getElementById('closeCropModal').addEventListener('click', closeCropModal);
document.getElementById('cancelCrop').addEventListener('click', closeCropModal);

function closeCropModal() {
  if (cropper) {
    cropper.destroy();
  }
  document.getElementById('cropModal').classList.add('hidden');
}

// Применение обрезки
document.getElementById('applyCrop').addEventListener('click', function() {
  if (cropper) {
    const canvas = cropper.getCroppedCanvas();
    currentImage.src = canvas.toDataURL();
    closeCropModal();
  }
});

// Изменение пропорций
document.querySelectorAll('.crop-ratio-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    if (!cropper) return;
    
    const ratio = this.dataset.ratio;
    if (ratio === 'free') {
      cropper.setAspectRatio(NaN);
    } else {
      cropper.setAspectRatio(eval(ratio));
    }
    
    document.querySelectorAll('.crop-ratio-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
  });
});