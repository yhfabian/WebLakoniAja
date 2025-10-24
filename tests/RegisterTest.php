<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../register_function.php';
require_once __DIR__ . '/../db.php';

class RegisterTest extends TestCase
{
    protected function setUp(): void
    {
        global $conn;
        // Bersihkan username test sebelumnya (agar tidak duplikat)
        mysqli_query($conn, "DELETE FROM konselor WHERE username='testuser'");
    }

    public function testValidRegistration()
    {
        $result = register(
            'Test User',
            'testuser',
            'password123',
            '123456',
            'Psikologi',
            'testuser@mail.com'
        );

        $this->assertTrue($result, "Registrasi valid seharusnya berhasil");
    }

    public function testDuplicateUsername()
    {
        register('Test User', 'testuser', 'password123', '123456', 'Psikologi', 'testuser@mail.com');

        $result = register('User Baru', 'testuser', 'password999', '987654', 'Konseling', 'userbaru@mail.com');

        $this->assertFalse($result, "Username duplikat seharusnya gagal");
    }

    public function testInvalidEmail()
    {
        $result = register(
            'Email Salah',
            'testemail',
            'password123',
            '123456',
            'Psikologi',
            'salah-email'
        );

        $this->assertFalse($result, "Email tidak valid seharusnya gagal");
    }

    protected function tearDown(): void
    {
        global $conn;
        mysqli_query($conn, "DELETE FROM konselor WHERE username IN ('testuser', 'testemail')");
    }
}
?>
