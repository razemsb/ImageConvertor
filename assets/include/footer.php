<footer class="bg-gradient-to-br from-gray-50 to-blue-50 border-t border-gray-200 mt-20">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-5">
                    <div class="flex items-center">
                        <img src="./assets/img/favicon/favicon-96x96.png" alt="Logo" class="h-10 w-10 mr-3 rounded-md">
                        <span
                            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">E-Convertor</span>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Профессиональный инструмент для конвертации изображений между различными форматами с
                        сохранением качества.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://github.com/razemsb" class="text-gray-500 hover:text-blue-600 transition-colors"
                            aria-label="GitHub">
                            <i class="fab fa-github text-xl"></i>
                        </a>
                        <a href="https://t.me/razemsb" class="text-gray-500 hover:text-blue-400 transition-colors"
                            aria-label="Telegram">
                            <i class="fab fa-telegram text-xl"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-compass mr-2 text-blue-500"></i> Навигация
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                Главная
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                Конвертер
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                История
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-chevron-right text-xs text-blue-400 mr-2"></i>
                                Настройки
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-book-open mr-2 text-blue-500"></i> Документация
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-file-alt text-sm text-blue-400 mr-2"></i>
                                Руководство
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-code text-sm text-blue-400 mr-2"></i>
                                API
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-question-circle text-sm text-blue-400 mr-2"></i>
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                                <i class="fas fa-bug text-sm text-blue-400 mr-2"></i>
                                Сообщить об ошибке
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i> Контакты
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-blue-400 mr-2"></i>
                            <a href="mailto:support@enigma-convertor.ru"
                                class="text-gray-600 hover:text-blue-600 transition-colors">
                                support@enigma-convertor.ru
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-blue-400 mr-2"></i>
                            <span class="text-gray-600">Поддержка 24/7</span>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="border-t border-gray-200 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-500 text-sm">
                        © <?= htmlspecialchars(date('Y')) ?> Enigma ImageConverter. Все права защищены.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Политика
                            конфиденциальности</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Условия
                            использования</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">Cookie</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>