package id.ac.univpancasila.perpustakaan.util;

import java.text.NumberFormat;
import java.time.LocalDate;
import java.time.format.DateTimeFormatter;
import java.util.Locale;

/**
 * Utilitas pemformatan string, mata uang, dan tanggal.
 */
public final class Format {

    private static final Locale LOCALE_ID = Locale.of("id", "ID");
    private static final DateTimeFormatter DATE_FORMATTER = DateTimeFormatter.ofPattern("dd MMMM yyyy", LOCALE_ID);

    private Format() {
        // Mencegah instansiasi class utility
    }

    public static String formatRupiah(long nominal) {
        NumberFormat nf = NumberFormat.getCurrencyInstance(LOCALE_ID);
        return nf.format(nominal);
    }

    public static String formatTanggal(LocalDate tanggal) {
        if (tanggal == null) {
            return "-";
        }
        return tanggal.format(DATE_FORMATTER);
    }
}
