# AutoText SDK PHP

Une bibliothèque PHP agnostique pour envoyer des SMS via Firebase Cloud Messaging (FCM) vers des appareils Android.

## 📋 Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [API](#api)
- [Développement](#développement)
- [Licence](#licence)

## ✨ Fonctionnalités

- ✅ Envoi de SMS via FCM vers des appareils Android
- ✅ Architecture orientée DTO (Data Transfer Objects)
- ✅ Gestion d'authentification Firebase
- ✅ Interface HTTP interchangeable (Guzzle par défaut)
- ✅ Factory pour une instanciation simplifiée
- ✅ Support des statuts d'appareils et de textos
- ✅ Enums type-safe pour tous les états
- ✅ Compatible PHP 8.1+

## 📦 Prérequis

- PHP 8.1 ou supérieur
- Composer
- Compte Firebase avec projet configuré
- Fichier de configuration Firebase (service account key)

## 🔧 Installation

```bash
composer require andydefer/autotext-sdk
```

## ⚙️ Configuration

### 1. Configuration Firebase

Créez un fichier de configuration Firebase :

```php
$firebaseConfig = [
    'project_id' => 'votre-project-id',
    'client_email' => 'votre-client-email@project.iam.gserviceaccount.com',
    'private_key' => '-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n',
    'token_uri' => 'https://oauth2.googleapis.com/token',
];
```

### 2. Configuration du SDK

```php
use Andydefer\AutotextSdk\Core\NotificationFactory;
use Andydefer\AutotextSdk\Services\GuzzleHttpClient;
use Andydefer\AutotextSdk\Services\FirebaseAuthProvider;
use Andydefer\AutotextSdk\Services\FcmPayloadBuilder;

// Initialiser les dépendances
$httpClient = new GuzzleHttpClient();
$authProvider = new FirebaseAuthProvider();
$payloadBuilder = new FcmPayloadBuilder();

// Créer la factory
$factory = new NotificationFactory(
    httpClient: $httpClient,
    authProvider: $authProvider,
    payloadBuilder: $payloadBuilder,
    config: $firebaseConfig
);
```

## 🚀 Utilisation

### Envoyer un SMS

```php
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Dtos\DeviceDto;
use Andydefer\AutotextSdk\Enums\TextoStatus;
use Andydefer\AutotextSdk\Enums\DeviceStatus;

// 1. Créer un DTO pour le texto
$texto = new TextoDto(
    id: 123,
    uuid: '550e8400-e29b-41d4-a716-446655440000',
    message: 'Bonjour, ceci est un test',
    phoneNumber: '+33612345678',
    status: TextoStatus::PENDING,
    deviceId: 1,
    retryCount: 0,
    lastAttemptAt: null,
    createdAt: '2025-12-10T13:45:30+00:00',
    updatedAt: '2025-12-10T13:45:30+00:00'
);

// 2. Créer un DTO pour l'appareil
$device = DeviceDto::fromArray([
    'id' => 'device-uuid-123',
    'api_key' => 'api-key-123',
    'status' => DeviceStatus::ONLINE->value,
    'fcm_id' => 'fcm-token-abc123',
    'last_connected_at' => '2025-12-10T13:45:30+00:00',
    'last_action_at' => '2025-12-10T13:45:30+00:00',
    'created_at' => '2025-12-10T13:45:30+00:00',
    'updated_at' => '2025-12-10T13:45:30+00:00',
    'is_recently_connected' => true,
    'is_recently_active' => true,
    'success_count' => 100,
    'failed_count' => 5,
    'success_rate' => 95,
]);

// 3. Récupérer le dispatcher depuis la factory
$dispatcher = $factory->makeDispatcher();

// 4. Envoyer le SMS
try {
    $result = $dispatcher->dispatch($texto, $device);

    if ($result) {
        echo "SMS envoyé avec succès !";
    } else {
        echo "Échec de l'envoi du SMS";
    }
} catch (\InvalidArgumentException $e) {
    echo "Erreur: " . $e->getMessage();
}
```

### Utilisation directe des services

```php
// Récupérer le service Firebase
$firebaseService = $factory->makeFirebaseService();

// Récupérer le sender SMS
$smsSender = $factory->makeSmsSender();

// Envoyer directement via le sender
$success = $smsSender->send($texto, $device->fcmId);
```

## 📚 API

### DTOs disponibles

#### `TextoDto`
Représente un texto à envoyer.

**Propriétés:**
- `id` (int): ID unique
- `uuid` (string): UUID unique
- `message` (string): Contenu du SMS
- `phoneNumber` (string): Numéro de téléphone
- `status` (TextoStatus): Statut du texto
- `deviceId` (int): ID de l'appareil
- `retryCount` (int): Nombre de tentatives
- `lastAttemptAt` (string|null): Dernière tentative (ISO8601)
- `createdAt` (string): Date de création (ISO8601)
- `updatedAt` (string): Date de modification (ISO8601)

**Méthodes:**
- `fromArray(array $data): self` - Crée un DTO depuis un tableau
- `toArray(): array` - Convertit en tableau

#### `DeviceDto`
Représente un appareil Android.

**Propriétés:**
- `id` (string): UUID de l'appareil
- `apiKey` (string): Clé API
- `status` (DeviceStatus): Statut de l'appareil
- `fcmId` (string|null): Token FCM
- `lastConnectedAt` (string|null): Dernière connexion (ISO8601)
- `lastActionAt` (string|null): Dernière action (ISO8601)
- `createdAt` (string): Date de création (ISO8601)
- `updatedAt` (string): Date de modification (ISO8601)
- Métriques supplémentaires: `isRecentlyConnected`, `isRecentlyActive`, etc.

#### `FcmMessageDto`
Représente un message FCM.

**Propriétés:**
- `actionType` (FcmActionType): Type d'action
- `message` (string): Contenu du message
- `phoneNumber` (string|null): Numéro pour SMS
- `smsId` (string|null): ID du SMS
- `timestamp` (string): Horodatage (ISO8601)

### Enums

#### `DeviceStatus`
- `ONLINE` - Appareil en ligne
- `OFFLINE` - Appareil hors ligne
- `ERROR` - Appareil en erreur

#### `TextoStatus`
- `PENDING` - SMS en attente
- `SUCCESS` - SMS envoyé avec succès
- `FAILED` - Échec d'envoi

#### `FcmActionType`
- `SEND_SMS` - Envoyer un SMS
- `INFO` - Message informatif
- `PING` - Ping de disponibilité
- `CONFIRM_SMS` - Confirmation d'envoi

### Services principaux

#### `NotificationFactory`
Factory centrale pour créer tous les services.

**Méthodes:**
- `makeFirebaseService(): FirebaseService`
- `makeSmsSender(): SmsSenderInterface`
- `makeDispatcher(): DeviceSmsDispatcher`

#### `DeviceSmsDispatcher`
Dispatch les SMS vers les appareils appropriés.

**Méthodes:**
- `dispatch(TextoDto $texto, DeviceDto $device): bool`

#### `FirebaseService`
Service Firebase pour l'envoi via FCM.

**Méthodes:**
- `send(FcmMessageDto $message, string $deviceToken): HttpResponseDto`
- `sendSmsToDevice(string $deviceToken, TextoDto $texto): HttpResponseDto`

## 🔧 Développement

### Structure du projet

```
src/
├── Contracts/              # Interfaces
│   ├── HttpClientInterface.php
│   └── SmsSenderInterface.php
├── Core/                   # Classes centrales
│   └── NotificationFactory.php
├── Dtos/                   # Data Transfer Objects
│   ├── TextoDto.php
│   ├── HttpResponseDto.php
│   ├── FcmMessageDto.php
│   └── DeviceDto.php
├── Enums/                  # Énumérations
│   ├── DeviceStatus.php
│   ├── FcmActionType.php
│   └── TextoStatus.php
└── Services/              # Services implémentés
    ├── GuzzleHttpClient.php
    ├── FirebaseService.php
    ├── FcmPayloadBuilder.php
    ├── DeviceSmsDispatcher.php
    ├── FirebaseSmsSender.php
    └── FirebaseAuthProvider.php
```

### Tests

```bash
# À venir
composer test
```

### Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/ma-feature`)
3. Commiter les changements (`git commit -am 'Ajout de ma feature'`)
4. Pusher la branche (`git push origin feature/ma-feature`)
5. Créer une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 🆘 Support

Pour les questions et le support :
- [Créer une issue](https://github.com/andydefer/autotext-sdk/issues)
- Documentation complète à venir

---

## 📝 Notes importantes

- Toutes les dates sont gérées en format ISO8601
- La bibliothèque est agnostique et peut être utilisée avec n'importe quel framework PHP
- Les DTOs sont immutables et type-safe
- L'interface `HttpClientInterface` permet de changer l'implémentation HTTP si nécessaire
- Les tokens FCM doivent être valides et à jour
- Le service Firebase nécessite une connexion internet active