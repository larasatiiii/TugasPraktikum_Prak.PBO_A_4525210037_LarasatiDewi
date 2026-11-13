<?php

namespace Perpustakaan\Exception;

use Perpustakaan\Exception\PerpustakaanException;

final class BukuTidakDitemukanException extends PerpustakaanException
{
    /**
     * Constructor property promotion: $isbn tersimpan sebagai properti readonly.
     */
    public function __construct(private readonly string $isbn)
    {
        parent::__construct("Buku dengan ISBN {$isbn} tidak ditemukan.");
    }

    // Getter agar pemanggil bisa mengambil kembali ISBN penyebabnya.
    public function getIsbn(): string
    {
        return $this->isbn;
    }
}
