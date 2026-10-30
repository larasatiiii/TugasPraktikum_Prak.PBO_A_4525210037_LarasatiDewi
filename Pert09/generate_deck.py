import sys
import os
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
from pptx.enum.shapes import MSO_SHAPE

def hex_to_rgb(hex_str):
    hex_str = hex_str.lstrip('#')
    return RGBColor(*(int(hex_str[i:i+2], 16) for i in (0, 2, 4)))

# Palet Warna Profesional
BG_DARK      = hex_to_rgb('#0F172A') # Slate 900
CARD_BG      = hex_to_rgb('#1E293B') # Slate 800
CARD_BORDER  = hex_to_rgb('#334155') # Slate 700
ACCENT_BLUE  = hex_to_rgb('#38BDF8') # Sky 400
ACCENT_GREEN = hex_to_rgb('#10B981') # Emerald 500
ACCENT_AMBER = hex_to_rgb('#F59E0B') # Amber 500
ACCENT_PURP  = hex_to_rgb('#818CF8') # Indigo 400
TEXT_WHITE   = hex_to_rgb('#F8FAFC') # Slate 50
TEXT_MUTED   = hex_to_rgb('#94A3B8') # Slate 400
CODE_BG      = hex_to_rgb('#020617') # Slate 950

def create_slide_deck(output_path):
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    def set_background(slide):
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = BG_DARK
        bg.line.color.rgb = BG_DARK
        return bg

    def add_header(slide, tag_text, title_text, category_color=ACCENT_BLUE):
        # Tag pill
        tag_box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.5), Inches(3.2), Inches(0.4))
        tag_box.fill.solid()
        tag_box.fill.fore_color.rgb = CARD_BG
        tag_box.line.color.rgb = category_color
        tag_box.line.width = Pt(1.5)
        tf = tag_box.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.text = tag_text.upper()
        p.font.size = Pt(11)
        p.font.bold = True
        p.font.color.rgb = category_color
        p.font.name = 'Arial'

        # Main Title
        title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.95), Inches(11.7), Inches(0.8))
        tf_t = title_box.text_frame
        tf_t.word_wrap = True
        p_t = tf_t.paragraphs[0]
        p_t.text = title_text
        p_t.font.size = Pt(24)
        p_t.font.bold = True
        p_t.font.color.rgb = TEXT_WHITE
        p_t.font.name = 'Arial'

    # =========================================================================
    # SLIDE 1: COVER
    # =========================================================================
    s1 = prs.slides.add_slide(blank_layout)
    set_background(s1)

    # Accent decorative banner
    banner = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.8), Inches(1.5), Inches(0.08))
    banner.fill.solid()
    banner.fill.fore_color.rgb = ACCENT_BLUE
    banner.line.color.rgb = ACCENT_BLUE

    title_box = s1.shapes.add_textbox(Inches(0.8), Inches(2.1), Inches(11.5), Inches(2.2))
    tf = title_box.text_frame
    tf.word_wrap = True
    p1 = tf.paragraphs[0]
    p1.text = "Package, Namespace, & Build Tool"
    p1.font.size = Pt(40)
    p1.font.bold = True
    p1.font.color.rgb = TEXT_WHITE
    p1.font.name = 'Arial'

    p2 = tf.add_paragraph()
    p2.text = "Restrukturisasi Proyek Flat Menjadi Arsitektur Standar Industri (Java & PHP)"
    p2.font.size = Pt(18)
    p2.font.color.rgb = ACCENT_BLUE
    p2.font.name = 'Arial'
    p2.space_before = Pt(12)

    # Info card bottom
    card = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(4.7), Inches(11.7), Inches(1.8))
    card.fill.solid()
    card.fill.fore_color.rgb = CARD_BG
    card.line.color.rgb = CARD_BORDER
    card.line.width = Pt(1)

    card_tf = card.text_frame
    card_tf.word_wrap = True
    cp1 = card_tf.paragraphs[0]
    cp1.text = "Panduan Sesi 9 — Praktikum Pemrograman Berorientasi Objek"
    cp1.font.size = Pt(14)
    cp1.font.bold = True
    cp1.font.color.rgb = TEXT_WHITE
    cp1.font.name = 'Arial'

    cp2 = card_tf.add_paragraph()
    cp2.text = "Universitas Pancasila · Durasi 170 Menit · Teknologi: Java 25 LTS, Maven 3.9, PHP 8.2+, Composer 2.x"
    cp2.font.size = Pt(12)
    cp2.font.color.rgb = TEXT_MUTED
    cp2.font.name = 'Arial'
    cp2.space_before = Pt(6)

    cp3 = card_tf.add_paragraph()
    cp3.text = "Target Capaian: Mampu menyusun package domain terbalik, autoload PSR-4, serta mengelola dependensi pihak ketiga secara deterministik."
    cp3.font.size = Pt(12)
    cp3.font.color.rgb = ACCENT_GREEN
    cp3.font.name = 'Arial'
    cp3.space_before = Pt(8)

    # =========================================================================
    # SLIDE 2: MASALAH AWAL (Sesi 1-7)
    # =========================================================================
    s2 = prs.slides.add_slide(blank_layout)
    set_background(s2)
    add_header(s2, "Konteks & Masalah", "Kenapa Kita Harus Membongkar Struktur Sesi 1 sampai 7?", ACCENT_AMBER)

    # Box Kiri: Kondisi Lama
    box_kiri = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(5.6), Inches(4.8))
    box_kiri.fill.solid()
    box_kiri.fill.fore_color.rgb = CARD_BG
    box_kiri.line.color.rgb = hex_to_rgb('#EF4444') # Red accent
    box_kiri.line.width = Pt(1.5)
    tf_kiri = box_kiri.text_frame
    tf_kiri.word_wrap = True
    kp1 = tf_kiri.paragraphs[0]
    kp1.text = "Kondisi Lama: Proyek Folder Datar"
    kp1.font.size = Pt(16)
    kp1.font.bold = True
    kp1.font.color.rgb = hex_to_rgb('#F87171')
    
    poin_kiri = [
        "Semua berkas kodingan menumpuk di satu folder utama tanpa pemisah kategori.",
        "Di PHP, kita terpaksa mengetik belasan baris 'require_once' secara manual di setiap berkas.",
        "Rentan terjadi tabrakan nama class (name collision). Dua class dengan nama sama tidak bisa hidup berdampingan.",
        "Jika butuh pustaka buatan orang lain, kita harus download file ZIP manual dan menaruhnya sembarangan di folder tugas.",
        "Sulit dikerjakan secara tim karena tidak ada standar tata letak berkas yang baku."
    ]
    for p in poin_kiri:
        para = tf_kiri.add_paragraph()
        para.text = "• " + p
        para.font.size = Pt(12)
        para.font.color.rgb = TEXT_WHITE
        para.space_before = Pt(10)

    # Box Kanan: Solusi Industri
    box_kanan = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    box_kanan.fill.solid()
    box_kanan.fill.fore_color.rgb = CARD_BG
    box_kanan.line.color.rgb = ACCENT_GREEN
    box_kanan.line.width = Pt(1.5)
    tf_kanan = box_kanan.text_frame
    tf_kanan.word_wrap = True
    rp1 = tf_kanan.paragraphs[0]
    rp1.text = "Solusi Sesi 9: Standar Industri"
    rp1.font.size = Pt(16)
    rp1.font.bold = True
    rp1.font.color.rgb = ACCENT_GREEN

    poin_kanan = [
        "Package (Java) & Namespace (PHP): Mengelompokkan berkas sesuai domain tanggung jawab (model, service, util).",
        "Autoload PSR-4: Cukup satu baris pendaftaran. PHP memuat class secara otomatis saat class tersebut dipanggil.",
        "Build Tools (Maven & Composer): Mengelola siklus kompilasi, pengujian, dan eksekusi program dalam satu perintah seragam.",
        "Central Repository: Pustaka luar diunduh otomatis dari Maven Central / Packagist lewat satu berkas catatan resmi.",
        "Fondasi Framework Modern: Struktur ini persis sama dengan yang dipakai Spring Boot (Sesi 14) dan Laravel (Sesi 13)."
    ]
    for p in poin_kanan:
        para = tf_kanan.add_paragraph()
        para.text = "✓ " + p
        para.font.size = Pt(12)
        para.font.color.rgb = TEXT_WHITE
        para.space_before = Pt(10)

    # =========================================================================
    # SLIDE 3: PERUMPAMAAN SEHARI-HARI
    # =========================================================================
    s3 = prs.slides.add_slide(blank_layout)
    set_background(s3)
    add_header(s3, "Konsep Inti", "Dua Analogi Sederhana untuk Memahami Materi Ini", ACCENT_PURP)

    # Card 1: Lemari Baju
    c1 = s3.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(5.6), Inches(4.8))
    c1.fill.solid()
    c1.fill.fore_color.rgb = CARD_BG
    c1.line.color.rgb = ACCENT_BLUE
    c1.line.width = Pt(1.5)
    tf1 = c1.text_frame
    tf1.word_wrap = True
    t1 = tf1.paragraphs[0]
    t1.text = "Analogi 1: Lemari Baju Bersekat"
    t1.font.size = Pt(16)
    t1.font.bold = True
    t1.font.color.rgb = ACCENT_BLUE

    items1 = [
        "Kasur Berantakan (Flat Folder): Semua baju, celana, jaket, dan kaos kaki dicampur aduk. Kalau barangnya cuma tiga masih mudah dicari. Begitu ada 50 barang, Anda kebingungan.",
        "Lemari Bersekat (Package / Namespace): Kita membuat laci khusus baju harian (folder model), laci jaket (folder service), dan laci aksesoris (folder util).",
        "Di Java, nama package adalah letak fisik laci tersebut. Jika Anda melabeli barang 'di laci atas' padahal barangnya di laci bawah, Java menolak program tersebut.",
        "Di PHP, namespace memberi identitas unik. Anda bisa punya class 'Buku' milik perpustakaan tanpa bentrok dengan class 'Buku' milik percetakan eksternal."
    ]
    for it in items1:
        p = tf1.add_paragraph()
        p.text = it
        p.font.size = Pt(12)
        p.font.color.rgb = TEXT_WHITE
        p.space_before = Pt(12)

    # Card 2: App Store / Play Store
    c2 = s3.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    c2.fill.solid()
    c2.fill.fore_color.rgb = CARD_BG
    c2.line.color.rgb = ACCENT_GREEN
    c2.line.width = Pt(1.5)
    tf2 = c2.text_frame
    tf2.word_wrap = True
    t2 = tf2.paragraphs[0]
    t2.text = "Analogi 2: Play Store & App Store"
    t2.font.size = Pt(16)
    t2.font.bold = True
    t2.font.color.rgb = ACCENT_GREEN

    items2 = [
        "Cara Lama (Manual): Mencari installer di Google, mendownload file ZIP mentah, mengekstraknya, lalu memindahkannya manual ke folder program. Rentan virus dan rusak saat update.",
        "Cara Modern (Maven & Composer): Anda cukup menulis daftar aplikasi yang Anda inginkan di selembar kertas catatan (pom.xml atau composer.json).",
        "Sistem yang Bekerja: Maven atau Composer bertindak sebagai App Store. Mereka menghubungi server pusat, mendownload paket yang pas, dan menyusunnya rapi di laptop Anda.",
        "Kunci Versi (composer.lock): Seperti mencatat nomor seri aplikasi. Jika teman sekelompok mendownload proyek Anda, mereka dijamin mendapat versi yang sama persis."
    ]
    for it in items2:
        p = tf2.add_paragraph()
        p.text = it
        p.font.size = Pt(12)
        p.font.color.rgb = TEXT_WHITE
        p.space_before = Pt(12)

    # =========================================================================
    # SLIDE 4: PEMERIKSAAN ALAT & INSTALASI
    # =========================================================================
    s4 = prs.slides.add_slide(blank_layout)
    set_background(s4)
    add_header(s4, "Persiapan Lab", "Langkah Awal: Memeriksa Alat dan Lingkungan Kerja", ACCENT_BLUE)

    card_tools = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(11.7), Inches(4.8))
    card_tools.fill.solid()
    card_tools.fill.fore_color.rgb = CARD_BG
    card_tools.line.color.rgb = CARD_BORDER
    tf_tool = card_tools.text_frame
    tf_tool.word_wrap = True

    tp1 = tf_tool.paragraphs[0]
    tp1.text = "Jalankan Perintah Pemeriksaan Ini Sebelum Mulai Koding"
    tp1.font.size = Pt(15)
    tp1.font.bold = True
    tp1.font.color.rgb = ACCENT_BLUE

    commands = [
        ("1. Java & Maven:", "java -version    # Wajib menampilkan Java 21 atau 25 LTS\nmvn -v           # Wajib menampilkan Apache Maven 3.9+"),
        ("2. PHP & Composer:", "php -v           # Wajib menampilkan PHP 8.2 atau 8.4+\ncomposer -v      # Wajib menampilkan Composer 2.x"),
        ("3. Catatan Kritis Ekstensi ZIP di PHP:", "Buka berkas php.ini (di folder XAMPP / Laragon).\nPastikan baris 'extension=zip' TIDAK diawali titik koma (;).\nJika ekstensi zip mati, Composer akan gagal mengekstrak paket dari internet.")
    ]

    for label, cmd in commands:
        p_lbl = tf_tool.add_paragraph()
        p_lbl.text = label
        p_lbl.font.size = Pt(13)
        p_lbl.font.bold = True
        p_lbl.font.color.rgb = TEXT_WHITE
        p_lbl.space_before = Pt(12)

        p_cmd = tf_tool.add_paragraph()
        p_cmd.text = cmd
        p_cmd.font.size = Pt(11)
        p_cmd.font.color.rgb = ACCENT_GREEN
        p_cmd.font.name = 'Consolas'
        p_cmd.space_before = Pt(4)

    # =========================================================================
    # SLIDE 5: ANATOMI JAVA & MAVEN
    # =========================================================================
    s5 = prs.slides.add_slide(blank_layout)
    set_background(s5)
    add_header(s5, "Bagian Java", "Struktur Folder Baku Maven dan Konfigurasi pom.xml", ACCENT_BLUE)

    # Kiri: Struktur Folder
    skiri = s5.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(5.6), Inches(4.8))
    skiri.fill.solid()
    skiri.fill.fore_color.rgb = CODE_BG
    skiri.line.color.rgb = CARD_BORDER
    tf_sk = skiri.text_frame
    tf_sk.word_wrap = True
    skp1 = tf_sk.paragraphs[0]
    skp1.text = "Peta Folder Baku (Wajib Dihafal)"
    skp1.font.size = Pt(13)
    skp1.font.bold = True
    skp1.font.color.rgb = ACCENT_BLUE

    tree_java = (
        "sesi-09/java/\n"
        "├── pom.xml\n"
        "└── src/main/java/\n"
        "    └── id/ac/univpancasila/perpustakaan/\n"
        "        ├── App.java\n"
        "        ├── model/\n"
        "        │   ├── Buku.java\n"
        "        │   ├── Anggota.java\n"
        "        │   └── Peminjaman.java\n"
        "        ├── service/\n"
        "        │   └── PerpustakaanService.java\n"
        "        └── util/\n"
        "            └── Format.java\n"
        "└── target/  (Otomatis terbuat saat compile)"
    )
    skp2 = tf_sk.add_paragraph()
    skp2.text = tree_java
    skp2.font.size = Pt(11)
    skp2.font.color.rgb = TEXT_WHITE
    skp2.font.name = 'Consolas'
    skp2.space_before = Pt(8)

    # Kanan: pom.xml
    skanan = s5.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    skanan.fill.solid()
    skanan.fill.fore_color.rgb = CARD_BG
    skanan.line.color.rgb = CARD_BORDER
    tf_skan = skanan.text_frame
    tf_skan.word_wrap = True
    skan_p1 = tf_skan.paragraphs[0]
    skan_p1.text = "Tiga Elemen Utama pom.xml"
    skan_p1.font.size = Pt(14)
    skan_p1.font.bold = True
    skan_p1.font.color.rgb = ACCENT_GREEN

    poin_pom = [
        "1. Koordinat Unik (G-A-V):\n• groupId: id.ac.univpancasila.perpustakaan (domain terbalik)\n• artifactId: perpustakaan\n• version: 1.0.0",
        "2. Target Kompiler Java:\n<properties>\n  <maven.compiler.release>25</maven.compiler.release>\n</properties>",
        "3. Dependensi Pihak Ketiga (ascii-table):\n<dependency>\n  <groupId>com.github.freva</groupId>\n  <artifactId>ascii-table</artifactId>\n  <version>1.8.0</version>\n</dependency>",
        "Maven otomatis mendownload library tersebut tanpa campur tangan manual kita."
    ]
    for p in poin_pom:
        para = tf_skan.add_paragraph()
        para.text = p
        para.font.size = Pt(11)
        para.font.color.rgb = TEXT_WHITE
        para.font.name = 'Consolas' if '<' in p else 'Arial'
        para.space_before = Pt(8)

    # =========================================================================
    # SLIDE 6: ANATOMI PHP & COMPOSER
    # =========================================================================
    s6 = prs.slides.add_slide(blank_layout)
    set_background(s6)
    add_header(s6, "Bagian PHP", "Autoload PSR-4 dan Manajemen Paket dengan composer.json", ACCENT_GREEN)

    # Kiri: Struktur Folder PHP
    pkiri = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(5.6), Inches(4.8))
    pkiri.fill.solid()
    pkiri.fill.fore_color.rgb = CODE_BG
    pkiri.line.color.rgb = CARD_BORDER
    tf_pk = pkiri.text_frame
    tf_pk.word_wrap = True
    pkp1 = tf_pk.paragraphs[0]
    pkp1.text = "Peta Folder Proyek Composer"
    pkp1.font.size = Pt(13)
    pkp1.font.bold = True
    pkp1.font.color.rgb = ACCENT_GREEN

    tree_php = (
        "sesi-09/php/\n"
        "├── composer.json    (Daftar dependensi & autoload)\n"
        "├── composer.lock    (Kunci versi eksak dependensi)\n"
        "├── main.php         (Titik masuk program utama)\n"
        "└── src/\n"
        "    ├── Model/\n"
        "    │   ├── Buku.php\n"
        "    │   ├── Bab.php\n"
        "    │   ├── Anggota.php\n"
        "    │   └── Peminjaman.php\n"
        "    ├── Service/\n"
        "    │   └── PerpustakaanService.php\n"
        "    └── Util/\n"
        "        └── Format.php\n"
        "└── vendor/          (Unduhan Composer, jangan di-push)"
    )
    pkp2 = tf_pk.add_paragraph()
    pkp2.text = tree_php
    pkp2.font.size = Pt(11)
    pkp2.font.color.rgb = TEXT_WHITE
    pkp2.font.name = 'Consolas'
    pkp2.space_before = Pt(8)

    # Kanan: composer.json
    pkanan = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    pkanan.fill.solid()
    pkanan.fill.fore_color.rgb = CARD_BG
    pkanan.line.color.rgb = CARD_BORDER
    tf_pkan = pkanan.text_frame
    tf_pkan.word_wrap = True
    pkan_p1 = tf_pkan.paragraphs[0]
    pkan_p1.text = "Cara Kerja Autoload PSR-4"
    pkan_p1.font.size = Pt(14)
    pkan_p1.font.bold = True
    pkan_p1.font.color.rgb = ACCENT_BLUE

    poin_comp = [
        "1. Pemetaan di composer.json:\n\"autoload\": {\n    \"psr-4\": {\n        \"Perpustakaan\\\\\": \"src/\"\n    }\n}",
        "2. Maksud Baris Tersebut:\nJika kita menulis 'new Perpustakaan\\Model\\Buku()', Composer otomatis mencari berkas ke jalur 'src/Model/Buku.php'.",
        "3. Bebas require_once:\nDi main.php cukup tulis SATU baris saja:\nrequire __DIR__ . '/vendor/autoload.php';\nSeluruh require_once manual lainnya dapat dihapus total.",
        "4. Dependensi nesbot/carbon:\nDigunakan untuk hitung jatuh tempo peminjaman dan selisih hari keterlambatan."
    ]
    for p in poin_comp:
        para = tf_pkan.add_paragraph()
        para.text = p
        para.font.size = Pt(11)
        para.font.color.rgb = TEXT_WHITE
        para.font.name = 'Consolas' if '{' in p or 'require' in p else 'Arial'
        para.space_before = Pt(8)

    # =========================================================================
    # SLIDE 7: EKSEKUSI & HASIL (Java & PHP)
    # =========================================================================
    s7 = prs.slides.add_slide(blank_layout)
    set_background(s7)
    add_header(s7, "Menjalankan Program", "Perintah Eksekusi dan Tampilan Hasil di Terminal", ACCENT_BLUE)

    # Box Java
    bj = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(5.6), Inches(4.8))
    bj.fill.solid()
    bj.fill.fore_color.rgb = CARD_BG
    bj.line.color.rgb = ACCENT_BLUE
    bj_tf = bj.text_frame
    bj_tf.word_wrap = True
    bjp1 = bj_tf.paragraphs[0]
    bjp1.text = "Eksekusi Proyek Java"
    bjp1.font.size = Pt(15)
    bjp1.font.bold = True
    bjp1.font.color.rgb = ACCENT_BLUE

    java_steps = (
        "Langkah di Terminal:\n"
        "cd done/java\n"
        "mvn clean compile\n"
        "mvn exec:java \"-Dexec.mainClass=id.ac.univpancasila.perpustakaan.App\"\n\n"
        "Hasil di Layar Terminal:\n"
        "+-------+---------------------------------+------+------------+\n"
        "| Kode  | Judul Buku                      | Stok | Jumlah Bab |\n"
        "+-------+---------------------------------+------+------------+\n"
        "| B-001 | Pemrograman Berorientasi Objek  |    4 |      3 bab |\n"
        "| B-002 | Struktur Data & Algoritma       |    2 |      2 bab |\n"
        "+-------+---------------------------------+------+------------+\n\n"
        "Tabel dirender rapi menggunakan pustaka eksternal ascii-table."
    )
    bjp2 = bj_tf.add_paragraph()
    bjp2.text = java_steps
    bjp2.font.size = Pt(10.5)
    bjp2.font.color.rgb = TEXT_WHITE
    bjp2.font.name = 'Consolas'
    bjp2.space_before = Pt(6)

    # Box PHP
    bp = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    bp.fill.solid()
    bp.fill.fore_color.rgb = CARD_BG
    bp.line.color.rgb = ACCENT_GREEN
    bp_tf = bp.text_frame
    bp_tf.word_wrap = True
    bpp1 = bp_tf.paragraphs[0]
    bpp1.text = "Eksekusi Proyek PHP"
    bpp1.font.size = Pt(15)
    bpp1.font.bold = True
    bpp1.font.color.rgb = ACCENT_GREEN

    php_steps = (
        "Langkah di Terminal:\n"
        "cd done/php\n"
        "composer install\n"
        "php main.php\n\n"
        "Hasil di Layar Terminal:\n"
        "ID     | Peminjam    | Judul Buku   | Jatuh Tempo | Denda\n"
        "-------+-------------+--------------+-------------+-------\n"
        "PJ-001 | Siti Aisyah | PBO Modul 9  | 03 Nov 2026 | Rp5.000\n\n"
        "Perhitungan Tanggal Otomatis:\n"
        "• Tanggal pinjam: 20 Oktober 2026\n"
        "• Jatuh tempo (+14 hari): 03 November 2026 (addDays)\n"
        "• Keterlambatan: 5 hari (diffInDays)\n"
        "• Total denda mahasiswa: 5 x Rp1.000 = Rp5.000"
    )
    bpp2 = bp_tf.add_paragraph()
    bpp2.text = php_steps
    bpp2.font.size = Pt(10.5)
    bpp2.font.color.rgb = TEXT_WHITE
    bpp2.font.name = 'Consolas'
    bpp2.space_before = Pt(6)

    # =========================================================================
    # SLIDE 8: DEMO MERUSAK KODE (Langkah 6 Modul)
    # =========================================================================
    s8 = prs.slides.add_slide(blank_layout)
    set_background(s8)
    add_header(s8, "Eksperimen Lab", "Langkah 6: Sengaja Merusak Kode untuk Memahami Diagnosa Error", hex_to_rgb('#EF4444'))

    # Box Percobaan PHP
    b_err_php = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(5.6), Inches(4.8))
    b_err_php.fill.solid()
    b_err_php.fill.fore_color.rgb = CARD_BG
    b_err_php.line.color.rgb = hex_to_rgb('#EF4444')
    b_err_php.line.width = Pt(1.5)
    tf_ep = b_err_php.text_frame
    tf_ep.word_wrap = True
    ep1 = tf_ep.paragraphs[0]
    ep1.text = "Percobaan 1 (PHP): Rusak Namespace"
    ep1.font.size = Pt(14)
    ep1.font.bold = True
    ep1.font.color.rgb = hex_to_rgb('#F87171')

    teks_ep = [
        "Aksi:\nUbah 'namespace Perpustakaan\\Model;' di Buku.php menjadi 'Perpustakaan\\Domain;' tanpa memindahkan berkasnya.",
        "Error Asli di Terminal:\nFatal error: Uncaught Error: Class 'Perpustakaan\\Model\\Buku' not found in main.php on line 23",
        "Penjelasan Masalah:\nAutoloader mencari ke folder Model karena namespace pemanggilnya Model. Begitu dibuka, isi class-nya Domain. PHP bingung dan menolak eksekusi.",
        "Solusi:\nSamakan kembali deklarasi namespace dengan nama foldernya, atau pindahkan berkas fisiknya ke folder 'src/Domain/'."
    ]
    for t in teks_ep:
        p = tf_ep.add_paragraph()
        p.text = t
        p.font.size = Pt(11)
        p.font.color.rgb = TEXT_WHITE
        p.space_before = Pt(8)

    # Box Percobaan Java
    b_err_java = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.9), Inches(5.7), Inches(4.8))
    b_err_java.fill.solid()
    b_err_java.fill.fore_color.rgb = CARD_BG
    b_err_java.line.color.rgb = hex_to_rgb('#EF4444')
    b_err_java.line.width = Pt(1.5)
    tf_ej = b_err_java.text_frame
    tf_ej.word_wrap = True
    ej1 = tf_ej.paragraphs[0]
    ej1.text = "Percobaan 2 (Java): Rusak Package"
    ej1.font.size = Pt(14)
    ej1.font.bold = True
    ej1.font.color.rgb = hex_to_rgb('#F87171')

    teks_ej = [
        "Aksi:\nUbah baris package di Buku.java menjadi 'id.ac.univpancasila.perpustakaan.domain;' tanpa memindahkan berkasnya.",
        "Error Asli Kompiler Javac:\n[ERROR] cannot access Buku.java\nbad source file: file does not contain class id.ac.univpancasila.perpustakaan.model.Buku\nPlease remove or make sure it appears in the correct subdirectory.",
        "Penjelasan Masalah:\nDi Java, package bukan saran atau label bebas. Ini aturan mutlak spesifikasi bahasa (JLS). Struktur folder wajib sama persis dengan deklarasi package.",
        "Solusi:\nKembalikan baris package ke 'id.ac.univpancasila.perpustakaan.model;'."
    ]
    for t in teks_ej:
        p = tf_ej.add_paragraph()
        p.text = t
        p.font.size = Pt(11)
        p.font.color.rgb = TEXT_WHITE
        p.space_before = Pt(8)

    # =========================================================================
    # SLIDE 9: ATURAN BERSIH GIT
    # =========================================================================
    s9 = prs.slides.add_slide(blank_layout)
    set_background(s9)
    add_header(s9, "Manajemen Repositori", "Aturan Penting Git: Apa yang Boleh dan Dilarang Di-commit", ACCENT_AMBER)

    card_git = s9.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(11.7), Inches(4.8))
    card_git.fill.solid()
    card_git.fill.fore_color.rgb = CARD_BG
    card_git.line.color.rgb = CARD_BORDER
    tf_git = card_git.text_frame
    tf_git.word_wrap = True

    gp1 = tf_git.paragraphs[0]
    gp1.text = "Pahami Alasan di Balik Berkas .gitignore"
    gp1.font.size = Pt(15)
    gp1.font.bold = True
    gp1.font.color.rgb = ACCENT_AMBER

    git_points = [
        ("DILARANG DI-COMMIT: target/ (Maven) dan vendor/ (Composer)",
         "• Ukuran Sangat Besar: Berisi ribuan berkas unduhan yang membuat ukuran riwayat Git membengkak drastis.\n"
         "• Mudah Digenerasi Ulang: Siapapun yang meng-clone proyek cukup mengetik 'mvn compile' atau 'composer install' untuk membuat folder ini lagi.\n"
         "• Masalah Lintas OS: Berkas biner di vendor/ pada Windows bisa menyebabkan error jika dijalankan di server Linux."),
        
        ("WAJIB DI-COMMIT: composer.lock (PHP) dan pom.xml (Java)",
         "• Jaminan Konsistensi Versi (Reproducibility): composer.json memakai simbol rentang seperti '^2.0'. Jika tanpa lock file, developer A bisa dapat versi 2.1 dan developer B dapat versi 2.8 yang mungkin mengandung bug baru.\n"
         "• composer.lock mengunci nomor seri versi dan hash komit persis dari setiap pustaka sehingga seluruh anggota tim menjalankan kode yang identik."),
         
        ("Perintah Penyelamat Jika Terlanjur Ter-push:",
         "git rm -r --cached target vendor\ngit commit -m 'Hapus artefak build dari git tracking'")
    ]

    for title, desc in git_points:
        pt = tf_git.add_paragraph()
        pt.text = title
        pt.font.size = Pt(13)
        pt.font.bold = True
        pt.font.color.rgb = hex_to_rgb('#F87171') if 'DILARANG' in title else (ACCENT_GREEN if 'WAJIB' in title else ACCENT_BLUE)
        pt.space_before = Pt(12)

        pd = tf_git.add_paragraph()
        pd.text = desc
        pd.font.size = Pt(11)
        pd.font.color.rgb = TEXT_WHITE
        pd.font.name = 'Consolas' if 'git rm' in desc else 'Arial'
        pd.space_before = Pt(3)

    # =========================================================================
    # SLIDE 10: KISI-KISI DEMO LISAN
    # =========================================================================
    s10 = prs.slides.add_slide(blank_layout)
    set_background(s10)
    add_header(s10, "Evaluasi Praktikum", "Kisi-kisi Demo Lisan (Bobot Nilai 25% — Menggugurkan)", ACCENT_PURP)

    card_qa = s10.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(11.7), Inches(4.8))
    card_qa.fill.solid()
    card_qa.fill.fore_color.rgb = CARD_BG
    card_qa.line.color.rgb = CARD_BORDER
    tf_qa = card_qa.text_frame
    tf_qa.word_wrap = True

    qp1 = tf_qa.paragraphs[0]
    qp1.text = "Pertanyaan Wajib yang Harus Mampu Dijawab Praktikan di Depan Asisten"
    qp1.font.size = Pt(14)
    qp1.font.bold = True
    qp1.font.color.rgb = ACCENT_PURP

    qas = [
        ("Q1: Bagaimana tahu letak folder berkas Java hanya dari membaca baris pertamanya?",
         "A: Lihat baris 'package id.ac.univpancasila.perpustakaan.model;'. Berkas itu PASTI ada di folder 'id/ac/univpancasila/perpustakaan/model/'."),
        ("Q2: Apa yang dikerjakan Composer saat program memanggil new Perpustakaan\\Model\\Buku()?",
         "A: Composer memotong prefix 'Perpustakaan\\' menjadi 'src/', mencari 'src/Model/Buku.php', lalu otomatis memuatnya ke memori."),
        ("Q3: Mengapa folder vendor/ tidak boleh di-commit, tetapi composer.lock harus?",
         "A: vendor/ bisa didownload ulang kapan saja dan ukurannya besar. composer.lock wajib disimpan agar seluruh tim memakai versi library yang sama persis."),
        ("Q4: Apa arti tanda ^2.0 pada composer.json? Apakah versi 3.0 akan ikut terpasang?",
         "A: Tanda caret (^) mengizinkan pembaruan minor (misal 2.1 atau 2.9), tetapi MENOLAK pembaruan mayor seperti 3.0 karena berisiko memecahkan kode lama.")
    ]

    for q, a in qas:
        pq = tf_qa.add_paragraph()
        pq.text = q
        pq.font.size = Pt(12)
        pq.font.bold = True
        pq.font.color.rgb = ACCENT_BLUE
        pq.space_before = Pt(8)

        pa = tf_qa.add_paragraph()
        pa.text = a
        pa.font.size = Pt(11)
        pa.font.color.rgb = TEXT_WHITE
        pa.space_before = Pt(2)

    # =========================================================================
    # SLIDE 11: CHECKLIST AKHIR PRAKTIKAN
    # =========================================================================
    s11 = prs.slides.add_slide(blank_layout)
    set_background(s11)
    add_header(s11, "Kesimpulan Sesi", "Checklist Pengumpulan Tugas Sebelum Mahasiswa Pulang", ACCENT_GREEN)

    card_check = s11.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.9), Inches(11.7), Inches(4.8))
    card_check.fill.solid()
    card_check.fill.fore_color.rgb = CARD_BG
    card_check.line.color.rgb = ACCENT_GREEN
    card_check.line.width = Pt(1.5)
    tf_ck = card_check.text_frame
    tf_ck.word_wrap = True

    cp_title = tf_ck.paragraphs[0]
    cp_title.text = "Daftar Periksa Kelengkapan Berkas Tugas Mahasiswa"
    cp_title.font.size = Pt(15)
    cp_title.font.bold = True
    cp_title.font.color.rgb = ACCENT_GREEN

    checklist_items = [
        "[ ] Folder 'java/' sudah rapi dengan pom.xml dan struktur 'src/main/java/id/ac/univpancasila/perpustakaan/'.",
        "[ ] mvn clean compile menghasilkan BUILD SUCCESS dan program jalan lewat mvn exec:java.",
        "[ ] Berkas 'dependency-tree.txt' sudah memuat bukti pemasangan pustaka ascii-table.",
        "[ ] Folder 'php/' memuat composer.json, composer.lock, dan src/ dengan autoload PSR-4 aktif.",
        "[ ] Program php main.php berjalan lancar tanpa satu pun require_once manual.",
        "[ ] Pustaka nesbot/carbon aktif digunakan untuk menghitung tanggal jatuh tempo dan denda.",
        "[ ] Berkas 'percobaan.md' memuat dua pesan error asli dari hasil percobaan merusak pemetaan.",
        "[ ] Repositori Git bersih: folder target/ dan vendor/ TIDAK muncul saat mengetik 'git status'."
    ]

    for item in checklist_items:
        p = tf_ck.add_paragraph()
        p.text = item
        p.font.size = Pt(12)
        p.font.color.rgb = TEXT_WHITE
        p.space_before = Pt(10)

    # Simpan presentasi
    os.makedirs(os.path.dirname(output_path), exist_ok=True)
    prs.save(output_path)
    print(f"Presentation successfully saved to: {output_path}")

if __name__ == '__main__':
    target_path = r"D:\FilePerkuliahan\AsprakPBO-A\modul praktikum\modul\09-package-namespace-dan-build-tool\done\Presentasi-Modul-09-Package-Namespace-BuildTool.pptx"
    create_slide_deck(target_path)
