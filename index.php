<?php
$additional_scripts = [
    './assets/js/script.js'
];
$additional_style = [];
$page_title = "Главная";
include "./assets/include/header.php";
?>
<div class="container mx-auto px-4 py-12">
    <div class="bg-white/30 backdrop-blur-xl rounded-3xl shadow-[0_0_80px_rgba(0,0,0,0.15)] overflow-hidden border border-white/10">
        <div class="flex flex-col sm:flex-row items-center justify-between px-10 py-10 bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-600 text-white">
            <div class="flex items-center space-x-6">
                <i class="fas fa-images text-5xl"></i>
                <div>
                    <h1 class="text-4xl font-bold">Конвертер изображений</h1>
                    <p class="text-sm text-white/80 mt-1">Поддержка популярных форматов и кастомного качества</p>
                </div>
            </div>
            <div class="mt-6 sm:mt-0">
                <span class="px-4 py-2 bg-white/10 rounded-xl text-sm font-medium border border-white/20">Beta</span>
            </div>
        </div>
        <div class="p-10 md:p-14">
            <div id="dropZone" class="drop-zone rounded-2xl p-12 text-center cursor-pointer">
                <input type="file" id="fileInput" multiple
                    accept="image/jpeg, image/png, image/gif, image/svg+xml, image/tiff, image/bmp" class="hidden">
                <div class="space-y-6">
                    <i class="fas fa-cloud-upload-alt text-7xl text-blue-500"></i>
                    <h4 class="text-2xl font-semibold text-gray-800">Перетащите изображения сюда</h4>
                    <p class="text-base text-gray-600">или нажмите для выбора файлов</p>
                    <p class="text-sm text-gray-500">Поддерживаются форматы: JPG, PNG, GIF</p>
                </div>
            </div>
            <div class="mt-12 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="glass-effect p-8 rounded-2xl">
                        <label class="block text-lg font-medium text-gray-800 mb-4">
                            <i class="fas fa-file-image mr-3 text-blue-500"></i>
                            Формат выходного файла
                        </label>
                        <div class="grid grid-cols-2 gap-4" id="formatSelector">
                            <label class="format-option">
                                <input type="radio" name="format" value="webp" checked class="hidden peer">
                                <div
                                    class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                    <i class="fas fa-file-code text-3xl text-blue-500 mb-2"></i>
                                    <span class="font-medium">WebP</span>
                                </div>
                            </label>
                            <label class="format-option">
                                <input type="radio" name="format" value="avif" class="hidden peer">
                                <div
                                    class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                    <i class="fas fa-file-image text-3xl text-purple-500 mb-2"></i>
                                    <span class="font-medium">AVIF</span>
                                </div>
                            </label>
                            <label class="format-option">
                                <input type="radio" name="format" value="jpeg" class="hidden peer">
                                <div
                                    class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                    <i class="fas fa-file-image text-3xl text-red-500 mb-2"></i>
                                    <span class="font-medium">JPEG</span>
                                </div>
                            </label>
                            <label class="format-option">
                                <input type="radio" name="format" value="png" class="hidden peer">
                                <div
                                    class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300">
                                    <i class="fas fa-file-image text-3xl text-blue-400 mb-2"></i>
                                    <span class="font-medium">PNG</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="glass-effect p-8 rounded-2xl">
                        <label class="block text-lg font-medium text-gray-800 mb-6">
                            <i class="fas fa-sliders-h mr-3 text-blue-500"></i>
                            Качество изображения
                        </label>
                        <div class="relative">
                            <input type="range" id="quality" name="quality"
                                class="quality-slider w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                min="0" max="100" value="90" step="5">
                            <div class="flex justify-between px-1 mt-3">
                                <span class="text-sm text-gray-500">0%</span>
                                <span class="text-sm text-gray-500">25%</span>
                                <span class="text-sm text-gray-500">50%</span>
                                <span class="text-sm text-gray-500">75%</span>
                                <span class="text-sm text-gray-500">100%</span>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-center">
                            <div class="text-3xl font-bold text-blue-600 quality-value mr-3">90%</div>
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-image text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
            <div id="progressBar" class="mt-8 hidden">
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full transition-all duration-300"
                        style="width: 0%"></div>
                </div>
            </div>
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-history mr-3 text-blue-500"></i>
                    История конвертаций
                    <button id="deleteHistory" class="ms-auto text-sm font-bold text-red-500 mb6 flex items-center">
                        <i class="fa-solid fa-trash-can me-1"></i>
                        Удалить всю историю
                    </button>
                </h2>
                <div id="history" class="space-y-4"></div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="popup-container" class="fixed bottom-4 right-4 space-y-2 z-50"></div>
<button id="scrollToTopBtn"
    class="fixed bottom-8 right-8 w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg transition-all duration-300 opacity-0 invisible flex items-center justify-center">
    <i class="fas fa-arrow-up text-xl"></i>
</button>
<?php include "./assets/include/footer.php"; ?>