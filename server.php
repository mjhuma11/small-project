<?php


class TVRemote {
    public string $brand;
    private int $batteryLevel;
    private bool $isOn;
    //channel number
    private int $channel=0;
    private int $volume=50;

    public function __construct(string $brand) {
        $this->brand = $brand;
        $this->batteryLevel = 100;
        $this->isOn = false;
    }

    public function turnOn(): string {
        if ($this->batteryLevel <= 0) return "❌ Battery dead - cannot turn on";
        $this->isOn = true;
        return "✅ TV is ON";
    }

    public function turnOff(): string {
        $this->isOn = false;
        return "⭕ TV is OFF";
    }

    public function changeBattery(): string {
        $this->batteryLevel = 100;
        return "🔋 Battery replaced (100%)";
    }

    public function checkBattery(): string {
        return $this->batteryLevel . "%";
    }

/*     public function pressButton(string $buttonName): string {
        if (!$this->isOn) return "⚠️ Turn on the TV first!";
        if ($this->batteryLevel <= 0) return "🪫 Replace battery!";

        $this->batteryLevel = max(0, $this->batteryLevel - 2); // Reduce battery on press
        
        $actions = [
            'power' => $this->turnOff(),
            'volumeup' => "🔊 Volume +",
            'volumedown' => "🔉 Volume -",
            'channelup' => "📡 Channel +",
            'channeldown' => "📡 Channel -",
            'mute' => "🔇 Muted"
        ];

        return $actions[strtolower($buttonName)] ?? "❓ Unknown button";
    } */
    public function pressButton(string $buttonName): string {
        if (!$this->isOn) return "⚠️ Turn on the TV first!";
        if ($this->batteryLevel <= 0) return "🪫 Replace battery!";
    
        $this->batteryLevel = max(0, $this->batteryLevel - 2); // Reduce battery
        
        // Handle power button separately
        if (strtolower($buttonName) === 'power') {
            return $this->turnOff(); // This will set isOn=false
        }
        
        $actions = [
            'volumeup' => "🔊 Volume +",
            'volumedown' => "🔉 Volume -", 
            'channelup' => "📡 Channel +",
            'channeldown' => "📡 Channel -",
            'mute' => "🔇 Muted"
        ];
        
        return $actions[strtolower($buttonName)] ?? "❓ Unknown button";
    }
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    session_start();
    if (!isset($_SESSION['remote'])) {
        $_SESSION['remote'] = new TVRemote("Sony");
    }
    $remote = $_SESSION['remote'];

    $response = [];
    switch ($_POST['action']) {
        case 'turnOn':
            $response['output'] = $remote->turnOn();
            break;
        case 'turnOff':
            $response['output'] = $remote->turnOff();
            break;
        case 'pressButton':
            $response['output'] = $remote->pressButton($_POST['button']);
            break;
        case 'changeBattery':
            $response['output'] = $remote->changeBattery();
            break;
        case 'checkBattery':
            $response['output'] = "Battery: " . $remote->checkBattery();
            break;
    }
    $response['battery'] = $remote->checkBattery();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>