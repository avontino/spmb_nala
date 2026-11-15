<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : Email tujuan untuk pengetesan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email uji coba untuk memverifikasi konfigurasi email di server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipient = $this->argument('email');
        $mailer = config('mail.default');
        $from = config('mail.from.address');
        $name = config('mail.from.name');

        $this->info("---------------------------------------------");
        $this->info("Menjalankan Uji Coba Pengiriman Email");
        $this->info("Mailer Driver : {$mailer}");
        $this->info("Dari (Sender) : {$name} <{$from}>");
        $this->info("Tujuan        : {$recipient}");
        $this->info("---------------------------------------------");

        try {
            Mail::raw(
                "Halo,\n\nIni adalah email uji coba dari SPMB SMAN Taruna Nala Jawa Timur.\nJika email ini sampai di kotak masuk Anda, maka konfigurasi email di server sudah BERHASIL dan SIAP digunakan untuk Reset Password & Notifikasi Pendaftar.\n\nWaktu kirim: " . now()->format('d F Y H:i:s') . "\n\nSalam,\nPanitia SPMB SMAN Taruna Nala Jawa Timur",
                function ($message) use ($recipient, $from, $name) {
                    $message->to($recipient)
                        ->from($from, $name)
                        ->subject('✅ [SUKSES] Uji Coba Pengiriman Email SPMB SMAN Taruna Nala');
                }
            );

            $this->info("🎉 BERHASIL: Email telah dikirimkan ke {$recipient}!");
            $this->info("Silakan periksa kotak masuk (Inbox) atau folder Spam pada email penerima.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ GAGAL: Terjadi kesalahan saat mengirim email.");
            $this->error("Pesan Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
