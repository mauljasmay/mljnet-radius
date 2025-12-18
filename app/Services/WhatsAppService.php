<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public $setting;
    public $provider;
    public $apiUrl;
    public $apiKey;
    public $sender;

    public function __construct()
    {
        $this->setting = IntegrationSetting::whatsapp();
        if ($this->setting && $this->setting->isActive()) {
            $this->provider = $this->setting->getConfig('provider');
            $this->apiUrl = $this->setting->getConfig('api_url');
            $this->apiKey = $this->setting->getConfig('api_key');
            $this->sender = $this->setting->getConfig('sender');
        }
    }

    /**
     * Send WhatsApp message based on provider
     */
    public function send($phone, $message)
    {
        $isActive = (isset($this->setting->enabled) && $this->setting->enabled) ||
                   (method_exists($this->setting, 'isActive') && $this->setting->isActive());

        if (!$this->setting || !$isActive) {
            return [
                'success' => false,
                'message' => 'WhatsApp integration is not configured or disabled'
            ];
        }

        try {
            $phone = $this->formatPhone($phone);

            switch ($this->provider) {
                case 'fonnte':
                    return $this->sendViaFonnte($phone, $message);
                case 'wablas':
                    return $this->sendViaWablas($phone, $message);
                case 'woowa':
                    return $this->sendViaWoowa($phone, $message);
                case 'mpwa':
                    return $this->sendViaMPWA($phone, $message);
                case 'custom':
                    return $this->sendViaCustom($phone, $message);
                default:
                    return [
                        'success' => false,
                        'message' => 'Unsupported WhatsApp provider: ' . $this->provider
                    ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send via Fonnte API
     */
    protected function sendViaFonnte($phone, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->asForm()->post($this->apiUrl . '/send', [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62',
        ]);

        $result = $response->json();

        if ($response->successful() && isset($result['status']) && $result['status'] === true) {
            Log::info('WhatsApp message sent via Fonnte', ['phone' => $phone]);
            return [
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $result
            ];
        }

        Log::error('WhatsApp send failed via Fonnte', [
            'phone' => $phone,
            'response' => $response->body()
        ]);

        return [
            'success' => false,
            'message' => $result['reason'] ?? 'Failed to send message',
            'error' => $response->body()
        ];
    }

    /**
     * Send via Wablas API
     */
    protected function sendViaWablas($phone, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->apiUrl . '/send-message', [
            'phone' => $phone,
            'message' => $message,
        ]);

        $result = $response->json();

        if ($response->successful() && isset($result['status']) && $result['status'] === true) {
            Log::info('WhatsApp message sent via Wablas', ['phone' => $phone]);
            return [
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $result
            ];
        }

        Log::error('WhatsApp send failed via Wablas', [
            'phone' => $phone,
            'response' => $response->body()
        ]);

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send message',
            'error' => $response->body()
        ];
    }

    /**
     * Send via Woowa API
     */
    protected function sendViaWoowa($phone, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->apiUrl . '/send', [
            'phone' => $phone,
            'message' => $message,
        ]);

        $result = $response->json();

        if ($response->successful() && isset($result['success']) && $result['success'] === true) {
            Log::info('WhatsApp message sent via Woowa', ['phone' => $phone]);
            return [
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $result
            ];
        }

        Log::error('WhatsApp send failed via Woowa', [
            'phone' => $phone,
            'response' => $response->body()
        ]);

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Failed to send message',
            'error' => $response->body()
        ];
    }

    /**
     * Send via MPWA API
     */
    protected function sendViaMPWA($phone, $message)
    {
        // Validate API configuration
        if (empty($this->apiUrl) || empty($this->apiKey)) {
            Log::error('MPWA API configuration missing', [
                'api_url' => $this->apiUrl ? 'set' : 'missing',
                'api_key' => $this->apiKey ? 'set' : 'missing'
            ]);
            return [
                'success' => false,
                'message' => 'MPWA API URL or API key is not configured.',
                'error' => 'Please check your MPWA integration settings.'
            ];
        }

        // Check if API URL already contains the endpoint
        $baseUrl = rtrim($this->apiUrl, '/');
        $urlContainsEndpoint = strpos($baseUrl, '/send-message') !== false;

        // Try MPWA API formats based on official documentation
        $attempts = [];

        if ($urlContainsEndpoint) {
            // If URL already contains endpoint, use it directly
            $attempts = [
                // Direct POST to the configured URL
                [
                    'method' => 'POST',
                    'endpoint' => '', // Empty endpoint since URL already contains it
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => [
                        'api_key' => $this->apiKey,
                        'sender' => $this->sender,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Direct GET to the configured URL
                [
                    'method' => 'GET',
                    'endpoint' => '', // Empty endpoint since URL already contains it
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'api_key' => $this->apiKey,
                        'sender' => $this->sender,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Form data format
                [
                    'method' => 'POST',
                    'endpoint' => '', // Empty endpoint since URL already contains it
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form' => [
                        'api_key' => $this->apiKey,
                        'sender' => $this->sender,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
            ];
        } else {
            // Standard MPWA format - append endpoint to base URL
            $attempts = [
                // Official MPWA format - POST with JSON body (primary)
                [
                    'method' => 'POST',
                    'endpoint' => '/send-message',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => [
                        'api_key' => $this->apiKey,
                        'sender' => $this->sender,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Official MPWA format - GET with query parameters
                [
                    'method' => 'GET',
                    'endpoint' => '/send-message',
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'api_key' => $this->apiKey,
                        'sender' => $this->sender,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Alternative POST format without sender (some MPWA implementations)
                [
                    'method' => 'POST',
                    'endpoint' => '/send-message',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => [
                        'api_key' => $this->apiKey,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Alternative GET format without sender
                [
                    'method' => 'GET',
                    'endpoint' => '/send-message',
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'api_key' => $this->apiKey,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Form data format (some MPWA implementations)
                [
                    'method' => 'POST',
                    'endpoint' => '/send-message',
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form' => [
                        'api_key' => $this->apiKey,
                        'sender' => $this->sender,
                        'number' => $phone,
                        'message' => $message,
                    ]
                ],
                // Legacy formats for backward compatibility
                [
                    'method' => 'POST',
                    'endpoint' => '/send',
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => [
                        'phone' => $phone,
                        'message' => $message,
                    ]
                ],
                [
                    'method' => 'POST',
                    'endpoint' => '/api/send-message',
                    'headers' => [
                        'apikey' => $this->apiKey,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'body' => [
                        'phone' => $phone,
                        'message' => $message,
                    ]
                ],
            ];
        }

        foreach ($attempts as $attempt) {
            try {
                $httpClient = Http::withHeaders($attempt['headers']);

                if ($attempt['method'] === 'GET') {
                    $response = $httpClient->get($this->apiUrl . $attempt['endpoint'], $attempt['query'] ?? []);
                } elseif (isset($attempt['form'])) {
                    $response = $httpClient->asForm()->post($this->apiUrl . $attempt['endpoint'], $attempt['form']);
                } else {
                    $response = $httpClient->post($this->apiUrl . $attempt['endpoint'], $attempt['body'] ?? []);
                }

                try {
                    $result = $response->json();
                    $isJsonResponse = $result !== null;
                } catch (\Exception $e) {
                    $result = null;
                    $isJsonResponse = false;
                }

                if ($response->successful() && $isJsonResponse) {
                    // Check for MPWA success indicators based on official docs
                    $isSuccess = (
                        (isset($result['status']) && ($result['status'] === true || $result['status'] === 1 || $result['status'] === 'true')) ||
                        (isset($result['success']) && ($result['success'] === true || $result['success'] === 1 || $result['success'] === 'true')) ||
                        (isset($result['msg']) && stripos(strtolower($result['msg']), 'success') !== false) ||
                        (isset($result['message']) && stripos(strtolower($result['message']), 'success') !== false)
                    );

                    if ($isSuccess) {
                        Log::info('WhatsApp message sent via MPWA', [
                            'phone' => $phone,
                            'endpoint' => $attempt['endpoint'],
                            'method' => $attempt['method'],
                            'sender' => $this->sender
                        ]);
                        return [
                            'success' => true,
                            'message' => $result['msg'] ?? $result['message'] ?? 'Message sent successfully',
                            'data' => $result
                        ];
                    }
                }

                // If we get a valid JSON response but not success, log it but continue trying
                if ($isJsonResponse) {
                    Log::warning('MPWA API returned JSON but not success', [
                        'endpoint' => $attempt['endpoint'],
                        'method' => $attempt['method'],
                        'response' => $result
                    ]);
                    continue;
                }

                // If HTML response, this might be the wrong endpoint
                if (strpos($response->body(), '<!DOCTYPE') === 0 || strpos($response->body(), '<html') === 0) {
                    Log::warning('MPWA API returned HTML for endpoint', ['endpoint' => $attempt['endpoint'], 'method' => $attempt['method']]);
                    continue;
                }

                // Log other responses for debugging
                Log::warning('MPWA API unexpected response', [
                    'endpoint' => $attempt['endpoint'],
                    'method' => $attempt['method'],
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);

            } catch (\Exception $e) {
                Log::warning('MPWA API attempt failed', [
                    'endpoint' => $attempt['endpoint'],
                    'method' => $attempt['method'],
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // All attempts failed
        Log::error('All MPWA API attempts failed', ['phone' => $phone, 'api_url' => $this->apiUrl, 'sender' => $this->sender]);
        return [
            'success' => false,
            'message' => 'Failed to send message via MPWA. Please check your API URL, key, sender number, and ensure the MPWA service is properly configured.',
            'error' => 'All API endpoint attempts failed. Please verify MPWA documentation for correct endpoint and authentication format.'
        ];
    }

    /**
     * Send via Custom API
     */
    protected function sendViaCustom($phone, $message)
    {
        // For custom API, use the same format as Fonnte as fallback
        return $this->sendViaFonnte($phone, $message);
    }

    /**
     * Send invoice notification
     */
    public function sendInvoiceNotification($customer, $invoice)
    {
        $message = "Halo *{$customer->name}*,\n\n";
        $message .= "Tagihan internet Anda telah terbit:\n\n";
        $message .= "📋 *Invoice:* {$invoice->invoice_number}\n";
        $message .= "📦 *Paket:* {$invoice->package->name}\n";
        $message .= "💰 *Total:* Rp " . number_format($invoice->amount, 0, ',', '.') . "\n";
        $message .= "📅 *Jatuh Tempo:* " . ($invoice->due_date ? $invoice->due_date->format('d M Y') : '-') . "\n\n";
        $message .= "Silakan lakukan pembayaran sebelum jatuh tempo.\n\n";
        $message .= "Terima kasih,\n";
        $message .= "*" . config('app.name') . "*";

        $result = $this->send($customer->phone, $message);
        $this->logMessage($customer->phone, 'invoice', $message, $result, $customer->id, $invoice->id);
        return $result;
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation($customer, $invoice)
    {
        $message = "Halo *{$customer->name}*,\n\n";
        $message .= "✅ Pembayaran Anda telah kami terima!\n\n";
        $message .= "📋 *Invoice:* {$invoice->invoice_number}\n";
        $message .= "💰 *Jumlah:* Rp " . number_format($invoice->amount, 0, ',', '.') . "\n";
        $message .= "📅 *Tanggal Bayar:* " . ($invoice->paid_date ? $invoice->paid_date->format('d M Y') : now()->format('d M Y')) . "\n\n";
        $message .= "Terima kasih atas pembayaran Anda.\n\n";
        $message .= "*" . config('app.name') . "*";

        return $this->send($customer->phone, $message);
    }

    /**
     * Send voucher to customer
     */
    public function sendVoucher($phone, $vouchers, $package)
    {
        $message = "🎫 *Voucher Internet Anda*\n\n";
        $message .= "Paket: *{$package}*\n\n";
        
        foreach ($vouchers as $index => $voucher) {
            $message .= "Voucher " . ($index + 1) . ":\n";
            $message .= "👤 Username: `{$voucher['code']}`\n";
            $message .= "🔑 Password: `{$voucher['password']}`\n\n";
        }
        
        $message .= "Cara pakai:\n";
        $message .= "1. Hubungkan ke WiFi\n";
        $message .= "2. Buka browser\n";
        $message .= "3. Masukkan username & password\n\n";
        $message .= "Terima kasih!\n";
        $message .= "*" . config('app.name') . "*";

        return $this->send($phone, $message);
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder($customer, $invoice)
    {
        $message = "⚠️ *Pengingat Pembayaran*\n\n";
        $message .= "Halo *{$customer->name}*,\n\n";
        $message .= "Tagihan Anda belum dibayar:\n\n";
        $message .= "📋 *Invoice:* {$invoice->invoice_number}\n";
        $message .= "💰 *Total:* Rp " . number_format($invoice->amount, 0, ',', '.') . "\n";
        $message .= "📅 *Jatuh Tempo:* " . ($invoice->due_date ? $invoice->due_date->format('d M Y') : '-') . "\n\n";
        $message .= "Mohon segera lakukan pembayaran untuk menghindari pemutusan layanan.\n\n";
        $message .= "*" . config('app.name') . "*";

        $result = $this->send($customer->phone, $message);
        $this->logMessage($customer->phone, 'reminder', $message, $result, $customer->id, $invoice->id);
        return $result;
    }

    /**
     * Send suspension notice
     */
    public function sendSuspensionNotice($customer)
    {
        $message = "🚫 *Pemberitahuan Penangguhan Layanan*\n\n";
        $message .= "Halo *{$customer->name}*,\n\n";
        $message .= "Layanan internet Anda telah ditangguhkan karena tunggakan pembayaran.\n\n";
        $message .= "Silakan hubungi kami atau lakukan pembayaran untuk mengaktifkan kembali layanan Anda.\n\n";
        $message .= "*" . config('app.name') . "*";

        $result = $this->send($customer->phone, $message);
        $this->logMessage($customer->phone, 'suspension', $message, $result, $customer->id);
        return $result;
    }

    /**
     * Log WhatsApp message
     */
    protected function logMessage($phone, $type, $message, $result, $customerId = null, $invoiceId = null)
    {
        try {
            WhatsappLog::create([
                'phone' => $phone,
                'type' => $type,
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'message' => $message,
                'status' => $result['success'] ? 'sent' : 'failed',
                'response' => $result['data'] ?? null,
                'error_message' => $result['success'] ? null : ($result['message'] ?? 'Unknown error'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log WhatsApp message: ' . $e->getMessage());
        }
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Check connection status based on provider
     */
    public function checkStatus()
    {
        if (!$this->setting || !$this->setting->isActive()) {
            return [
                'connected' => false,
                'message' => 'WhatsApp integration is not configured or disabled'
            ];
        }

        try {
            switch ($this->provider) {
                case 'fonnte':
                    return $this->checkStatusFonnte();
                case 'wablas':
                    return $this->checkStatusWablas();
                case 'woowa':
                    return $this->checkStatusWoowa();
                case 'mpwa':
                    return $this->checkStatusMPWA();
                case 'custom':
                    return $this->checkStatusCustom();
                default:
                    return [
                        'connected' => false,
                        'message' => 'Unsupported provider for status check: ' . $this->provider
                    ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp status check failed: ' . $e->getMessage());
            return [
                'connected' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Check status via Fonnte API
     */
    protected function checkStatusFonnte()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->apiUrl . '/device');

        $result = $response->json();

        if ($response->successful() && isset($result['status']) && $result['status'] === true) {
            return [
                'connected' => true,
                'device' => $result['device'] ?? null,
                'status' => $result
            ];
        }

        return [
            'connected' => false,
            'message' => $result['reason'] ?? 'Device not connected'
        ];
    }

    /**
     * Check status via Wablas API
     */
    protected function checkStatusWablas()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get($this->apiUrl . '/device/info');

        $result = $response->json();

        if ($response->successful() && isset($result['status']) && $result['status'] === true) {
            return [
                'connected' => true,
                'device' => $result['device'] ?? null,
                'status' => $result
            ];
        }

        return [
            'connected' => false,
            'message' => $result['message'] ?? 'Device not connected'
        ];
    }

    /**
     * Check status via Woowa API
     */
    protected function checkStatusWoowa()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get($this->apiUrl . '/status');

        $result = $response->json();

        if ($response->successful() && isset($result['connected']) && $result['connected'] === true) {
            return [
                'connected' => true,
                'device' => $result['device'] ?? null,
                'status' => $result
            ];
        }

        return [
            'connected' => false,
            'message' => $result['message'] ?? 'Device not connected'
        ];
    }

    /**
     * Check status via MPWA API
     */
    protected function checkStatusMPWA()
    {
        // Try common status endpoints for MPWA with different auth methods
        $attempts = [
            [
                'endpoint' => '/status',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ]
            ],
            [
                'endpoint' => '/api/status',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ]
            ],
            [
                'endpoint' => '/device/status',
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ]
            ],
            [
                'endpoint' => '/status',
                'headers' => [
                    'apikey' => $this->apiKey,
                    'Accept' => 'application/json',
                ]
            ],
            [
                'endpoint' => '/api/status',
                'headers' => [
                    'apikey' => $this->apiKey,
                    'Accept' => 'application/json',
                ]
            ],
        ];

        foreach ($attempts as $attempt) {
            $response = Http::withHeaders($attempt['headers'])->get($this->apiUrl . $attempt['endpoint']);

            try {
                $result = $response->json();
                $isJsonResponse = $result !== null;
            } catch (\Exception $e) {
                $result = null;
                $isJsonResponse = false;
            }

            if ($response->successful() && $isJsonResponse) {
                // Check for various connection indicators
                $isConnected = (
                    (isset($result['connected']) && ($result['connected'] === true || $result['connected'] === 1)) ||
                    (isset($result['status']) && strtolower($result['status']) === 'connected') ||
                    (isset($result['online']) && ($result['online'] === true || $result['online'] === 1)) ||
                    (isset($result['active']) && ($result['active'] === true || $result['active'] === 1))
                );

                if ($isConnected) {
                    return [
                        'connected' => true,
                        'device' => $result['device'] ?? $result['phone'] ?? null,
                        'status' => $result
                    ];
                }

                // If we get a valid JSON response, use it even if not connected
                return [
                    'connected' => false,
                    'message' => $result['message'] ?? $result['error'] ?? 'Device not connected'
                ];
            }
        }

        return [
            'connected' => false,
            'message' => 'Unable to check MPWA status. Please verify API URL and credentials.'
        ];
    }

    /**
     * Check status via Custom API
     */
    protected function checkStatusCustom()
    {
        // For custom API, assume connected if API key is set
        return [
            'connected' => !empty($this->apiKey),
            'message' => !empty($this->apiKey) ? 'Custom API configured' : 'API key not set'
        ];
    }

    /**
     * Check if service is connected
     */
    public function isConnected()
    {
        $status = $this->checkStatus();
        return $status && isset($status['connected']) && $status['connected'] === true;
    }

    /**
     * Set temporary settings for testing (used by controller)
     */
    public function setTestSettings($provider, $apiUrl, $apiKey)
    {
        $this->provider = $provider;
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->setting = (object) ['enabled' => true]; // Mock active setting
    }
}
