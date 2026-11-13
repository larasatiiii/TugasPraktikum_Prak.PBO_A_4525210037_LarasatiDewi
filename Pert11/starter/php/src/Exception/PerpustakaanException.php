<?php

namespace Perpustakaan\Exception;

use RuntimeException;

/**
 * Sesi 10 — hierarki exception domain (PHP).
 *
 * PHP tidak mengenal checked exception; seluruh exception setara dengan
 * unchecked di Java. Karena itu anotasi @throws pada PHPDoc menjadi jauh
 * lebih penting: pembaca kode tidak dibantu kompilator.
 */
abstract class PerpustakaanException extends RuntimeException
{
}
