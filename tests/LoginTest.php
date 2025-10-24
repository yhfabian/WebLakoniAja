<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../login_function.php';
require_once __DIR__ . '/../db.php';

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        global $conn;

        // Hapus user test jika sudah ada
        mysqli_query($conn, "DELETE FROM konselor WHERE username='testlogin'");

        // Tambah user test ke DB
        $hashed = password_hash('password123', PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO konselor (nama, username, password, nip, bidang_keahlian, kontak)
                             VALUES ('Tester Login', 'testlogin', '$hashed', '111222', 'Konseling', 'testlogin@mail.com')");
    }

    public function testValidLogin()
    {
        $result = login('testlogin', 'password123');
        $this->assertTrue($result, "Login valid seharusnya berhasil");
    }

    public function testInvalidPassword()
    {
        $result = login('testlogin', 'salahpassword');
        $this->assertFalse($result, "Login dengan password salah seharusnya gagal");
    }

    public function testInvalidUsername()
    {
        $result = login('tidakadauser', 'password123');
        $this->assertFalse($result, "Login dengan username salah seharusnya gagal");
    }

    protected function tearDown(): void
    {
        global $conn;
        mysqli_query($conn, "DELETE FROM konselor WHERE username='testlogin'");
    }
}
?>
