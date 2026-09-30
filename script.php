#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Есть список директорий неизвестно насколько большой вложенности
 * В директории может быть файл count
 * Нужно пройтись по всем директориям и вернуть сумму всех чисел из файла count
 * (файлов count может быть много)
 * Уточнение: будут натуральные числа (без дробей) разделенные запятыми. 
 * например директорая 1 файл1 -1,2,3  директория 2 в директории 1 файл2 - 4,5,6 директория 3 файл 3 - 7,8,9 = 45 то есть сумма всех чисел.
 */

echo "Please enter the path to folder: ";

$folder = trim(fgets(STDIN));

/* Проверяем, что это - папка, и она существует
 Если её нет, можем пойти двумя путями: 
 выдать 0 (чисел нет, значит, их сумма === 0) или ошибку.
 Данный пример кода выводит ошибку. */

if (!is_dir($folder)) {
    echo "Wrong folder name";
    exit(1);
}

echo sumCountValues($folder);

// Алгоритм обхода и суммирования
function sumCountValues(string $folder)
{
    $result = 0;
    // нет прав на чтение
    try {
        $iterator = new DirectoryIterator($folder);
    } catch (UnexpectedValueException $e) {
        return;
    }

    foreach ($iterator as $entry) {
        // отсекаем . и ..
        if ($entry->isDot()) {
            continue;
        }

        $path = $entry->getPathname();

        /* если это файл с именем count:
         берём содержимое, 
         помещаем в массив, отделив заранее известным разделителем и 
         суммируем */
        if ($entry->isFile() && pathinfo($path, PATHINFO_FILENAME) === 'count') {
            $content = file_get_contents($path);
            if ($content !== false) {
                foreach (explode(',', trim($content)) as $number) {
                    $result += (int) $number;
                }
            }
            continue;
        }

        // если это директория - уходим в рекурсию
        if ($entry->isDir()) {
            $result += sumCountValues($path);
        }
    }

    return $result;
}
