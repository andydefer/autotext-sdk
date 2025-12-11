# AutoText SDK PHP

Une bibliothèque PHP robuste et type-safe pour envoyer des SMS via Firebase Cloud Messaging (FCM) vers des appareils Android. Architecture orientée DTO avec injection de dépendances.

## 🚀 Caractéristiques principales

- ✅ **Envoi de SMS via FCM** - Communication directe avec les appareils Android
- ✅ **Architecture SOLID** - Interfaces, DTOs immutables, injection de dépendances
- ✅ **Type-safe avec Enums PHP** - Validation au niveau du langage
- ✅ **Factory Pattern** - Instanciation centralisée et configurable
- ✅ **HTTP Client interchangeable** - Support Guzzle par défaut, extensible
- ✅ **Gestion complète d'authentification Firebase** - Tokens JWT automatiques
- ✅ **Compatibilité PHP 8.1+** - Utilise les dernières fonctionnalités PHP

## 📦 Installation

```bash
composer require andydefer/autotext-sdk
```

## 🎯 Cas d'utilisation

- Systèmes de notification SMS en temps réel
- Applications de marketing par SMS
- Systèmes d'alerte et de notification
- Intégration avec des appareils Android distants
- Microservices de messagerie

## ⚙️ Configuration minimale

### 1. Obtenir les credentials Firebase

```php
// firebase-config.php
return [
    'project_id' => 'votre-project-firebase',
    'client_email' => 'service-account@project.iam.gserviceaccount.com',
    'private_key' => '-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n',
    'token_uri' => 'https://oauth2.googleapis.com/token',
];
```

### 2. Initialiser le SDK

```php
use Andydefer\AutotextSdk\Core\NotificationFactory;
use Andydefer\AutotextSdk\Services\GuzzleHttpClient;
use Andydefer\AutotextSdk\Services\FirebaseAuthProvider;
use Andydefer\AutotextSdk\Services\FcmPayloadBuilder;

$config = require 'firebase-config.php';

$factory = new NotificationFactory(
    new GuzzleHttpClient(),
    new FirebaseAuthProvider(),
    new FcmPayloadBuilder(),
    $config
);
```

## 📖 Guide d'utilisation

### Envoyer un SMS en 4 étapes

```php
use Andydefer\AutotextSdk\Dtos\{TextoDto, DeviceDto};
use Andydefer\AutotextSdk\Enums\{TextoStatus, DeviceStatus};

// 1. Préparer le texto
$texto = TextoDto::fromArray([
    'id' => 123,
    'uuid' => '550e8400-e29b-41d4-a716-446655440000',
    'message' => 'Votre code de vérification est: 123456',
    'phone_number' => '+33612345678',
    'status' => TextoStatus::PENDING->value,
    'device_id' => 1,
    'retry_count' => 0,
    'last_attempt_at' => null,
    'created_at' => date('c'),
    'updated_at' => date('c'),
]);

// 2. Préparer l'appareil cible
$device = DeviceDto::fromArray([
    'id' => 'device-android-001',
    'api_key' => 'ak-123456789',
    'status' => DeviceStatus::ONLINE->value,
    'fcm_id' => 'fcm-token-actuel-123',
    'last_connected_at' => date('c', strtotime('-5 minutes')),
    'last_action_at' => date('c', strtotime('-2 minutes')),
    'created_at' => date('c', strtotime('-30 days')),
    'updated_at' => date('c'),
    'is_recently_connected' => true,
    'is_recently_active' => true,
    'success_count' => 150,
    'failed_count' => 3,
    'success_rate' => 98,
]);

// 3. Obtenir le dispatcher
$dispatcher = $factory->makeDispatcher();

// 4. Envoyer le SMS
try {
    $result = $dispatcher->dispatch($texto, $device);

    if ($result) {
        echo "✅ SMS envoyé avec succès à {$texto->phoneNumber}";
    } else {
        echo "❌ Échec de l'envoi du SMS";
    }
} catch (InvalidArgumentException $e) {
    echo "⚠️ Erreur de validation: " . $e->getMessage();
} catch (Exception $e) {
    echo "🔥 Erreur système: " . $e->getMessage();
}
```

### Utilisation avancée

```php
// Récupérer des services individuels
$firebaseService = $factory->makeFirebaseService();
$smsSender = $factory->makeSmsSender();

// Vérifier la connectivité d'un appareil
try {
    $pingResponse = $firebaseService->pingDevice($device->fcmId);

    if ($pingResponse->isSuccess()) {
        echo "📱 Appareil {$device->id} est en ligne";
    }
} catch (Exception $e) {
    echo "📵 Appareil {$device->id} hors ligne";
}

// Envoyer un message informatif
use Andydefer\AutotextSdk\Dtos\FcmMessageDto;
use Andydefer\AutotextSdk\Enums\FcmActionType;

$infoMessage = new FcmMessageDto(
    actionType: FcmActionType::INFO,
    message: 'Mise à jour système prévue à 02:00',
);

$response = $firebaseService->send($infoMessage, $device->fcmId);
```

## 🏗 Architecture

### Structure des DTOs (Data Transfer Objects)

```php
// Tous les DTOs suivent le même pattern:
// - Constructeur avec propriétés publiques readonly
// - Méthodes fromArray() et toArray() pour la sérialisation
// - Typage strict avec Enums

$texto = new TextoDto(
    id: 123,
    uuid: '...',
    message: '...',
    phoneNumber: '+336...',
    status: TextoStatus::PENDING, // Enum
    // ...
);

// Conversion depuis un tableau (utile pour les APIs)
$texto = TextoDto::fromArray($_POST['texto']);

// Conversion vers tableau (pour le stockage/API)
$data = $texto->toArray();
```

### Interfaces et contrats

```php
// HttpClientInterface - Interchangeable HTTP client
interface HttpClientInterface {
    public function post(string $url, array $options): HttpResponseDto;
}

// SmsSenderInterface - Abstraction pour l'envoi de SMS
interface SmsSenderInterface {
    public function send(TextoDto $texto, string $deviceFcmToken): bool;
}

// Implémentation personnalisée possible:
class CustomHttpClient implements HttpClientInterface {
    // Votre logique HTTP
}
```

## 🔧 Services disponibles

### `DeviceSmsDispatcher`
Gestionnaire principal d'envoi de SMS avec validation.

```php
$dispatcher = $factory->makeDispatcher();

// Validation automatique:
// - Vérifie que l'appareil est ONLINE
// - Vérifie la présence du token FCM
// - Gère les exceptions de validation

try {
    $dispatcher->dispatch($texto, $device);
} catch (InvalidArgumentException $e) {
    // Gérer les erreurs de validation
}
```

### `FirebaseService`
Service Firebase complet avec gestion de tokens.

```php
$firebaseService = $factory->makeFirebaseService();

// Gestion automatique des tokens JWT
// Renouvellement transparent
// Configuration centralisée

// Envoyer un SMS
$response = $firebaseService->sendSmsToDevice($fcmToken, $texto);

// Envoyer un ping
$response = $firebaseService->pingDevice($fcmToken);

// Envoyer un message custom
$response = $firebaseService->send($fcmMessage, $fcmToken);
```

### `FcmPayloadBuilder`
Constructeur de payloads FCM optimisés.

```php
// Configure automatiquement:
// - Priorité Android: 'high'
// - Configuration APNs pour iOS
// - Serialisation des données
// - Headers appropriés

$payload = $payloadBuilder->build($fcmMessage);
// {
//   "message": {
//     "token": "fcm-token",
//     "data": { ... },
//     "android": {"priority": "high"},
//     "apns": { ... }
//   }
// }
```

## 📊 Gestion des états

### Statuts d'appareil (`DeviceStatus`)

```php
use Andydefer\AutotextSdk\Enums\DeviceStatus;

// Vérifications type-safe
if ($device->status === DeviceStatus::ONLINE) {
    // Appareil disponible
}

if ($device->status === DeviceStatus::OFFLINE) {
    // Mettre en file d'attente
}

if ($device->status === DeviceStatus::ERROR) {
    // Journaliser l'erreur
}
```

### Statuts de texto (`TextoStatus`)

```php
use Andydefer\AutotextSdk\Enums\TextoStatus;

// Workflow complet d'un SMS
$texto->status = TextoStatus::PENDING;   // Initial
$texto->status = TextoStatus::SUCCESS;   // Après envoi réussi
$texto->status = TextoStatus::FAILED;    // Après échec

// Utilisation dans des conditions
switch ($texto->status) {
    case TextoStatus::PENDING:
        // Traitement en attente
        break;
    case TextoStatus::SUCCESS:
        // Confirmer l'envoi
        break;
    case TextoStatus::FAILED:
        // Gérer la réessai
        break;
}
```

## 🛠 Intégration avec différents frameworks

### Laravel

```php
// Service Provider
class AutotextServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NotificationFactory::class, function ($app) {
            return new NotificationFactory(
                new GuzzleHttpClient(),
                new FirebaseAuthProvider(),
                new FcmPayloadBuilder(),
                config('services.firebase')
            );
        });
    }
}

// Utilisation dans un Controller
class SmsController extends Controller
{
    public function send(SendSmsRequest $request, NotificationFactory $factory)
    {
        $texto = TextoDto::fromArray($request->validated());
        $device = DeviceDto::fromArray($request->device_data);

        return $factory->makeDispatcher()->dispatch($texto, $device);
    }
}
```

### Symfony

```yaml
# services.yaml
services:
    Andydefer\AutotextSdk\Services\GuzzleHttpClient: ~
    Andydefer\AutotextSdk\Services\FirebaseAuthProvider: ~
    Andydefer\AutotextSdk\Services\FcmPayloadBuilder: ~

    Andydefer\AutotextSdk\Core\NotificationFactory:
        arguments:
            $httpClient: '@Andydefer\AutotextSdk\Services\GuzzleHttpClient'
            $authProvider: '@Andydefer\AutotextSdk\Services\FirebaseAuthProvider'
            $payloadBuilder: '@Andydefer\AutotextSdk\Services\FcmPayloadBuilder'
            $config: '%env(json:FIREBASE_CONFIG)%'
```

## 🧪 Tests et qualité

### Structure de test recommandée

```
tests/
├── Unit/
│   ├── Dtos/
│   │   ├── TextoDtoTest.php
│   │   ├── DeviceDtoTest.php
│   │   └── FcmMessageDtoTest.php
│   ├── Services/
│   │   ├── DeviceSmsDispatcherTest.php
│   │   └── FirebaseServiceTest.php
│   └── Enums/
│       └── DeviceStatusTest.php
└── Feature/
    ├── DeviceSmsDispatcherFeatureTest.php
    └── FirebaseIntegrationTest.php
```

### Exemple de test unitaire

```php
use PHPUnit\Framework\TestCase;
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Enums\TextoStatus;

class TextoDtoTest extends TestCase
{
    public function test_can_create_texto_dto_from_array()
    {
        $data = [
            'id' => 1,
            'uuid' => 'test-uuid',
            'message' => 'Test message',
            'phone_number' => '+33612345678',
            'status' => 'pending',
            'device_id' => 1,
            'retry_count' => 0,
            'last_attempt_at' => null,
            'created_at' => '2025-12-10T10:00:00+00:00',
            'updated_at' => '2025-12-10T10:00:00+00:00',
        ];

        $texto = TextoDto::fromArray($data);

        $this->assertEquals(1, $texto->id);
        $this->assertEquals('Test message', $texto->message);
        $this->assertEquals(TextoStatus::PENDING, $texto->status);
    }
}
```

## 🔍 Dépannage

### Problèmes courants

1. **Token FCM invalide**
   ```php
   // Vérifier le token avant utilisation
   if (empty($device->fcmId)) {
       throw new \RuntimeException('Token FCM manquant');
   }
   ```

2. **Erreur d'authentification Firebase**
   ```php
   // Vérifier les credentials
   $required = ['project_id', 'client_email', 'private_key'];
   foreach ($required as $key) {
       if (empty($config[$key])) {
           throw new \InvalidArgumentException("Missing Firebase config: $key");
       }
   }
   ```

3. **Appareil hors ligne**
   ```php
   // Vérifier le statut avant envoi
   if ($device->status !== DeviceStatus::ONLINE) {
       // Mettre en file d'attente ou journaliser
       $this->logger->warning("Device {$device->id} is offline");
   }
   ```

## 📈 Bonnes pratiques

### 1. Gestion des erreurs

```php
try {
    $result = $dispatcher->dispatch($texto, $device);

    if (!$result) {
        // Incrémenter le compteur de réessais
        $texto->retryCount++;
        $texto->lastAttemptAt = date('c');

        // Journaliser l'échec
        $this->logger->error('SMS dispatch failed', [
            'texto_id' => $texto->id,
            'device_id' => $device->id,
        ]);
    }
} catch (InvalidArgumentException $e) {
    // Erreur de validation - ne pas réessayer
    $this->logger->critical($e->getMessage());
} catch (Exception $e) {
    // Erreur système - réessayer plus tard
    $this->retryQueue->push($texto);
}
```

### 2. Monitoring et métriques

```php
// Suivre les performances
$startTime = microtime(true);
$result = $dispatcher->dispatch($texto, $device);
$duration = microtime(true) - $startTime;

// Envoyer des métriques
$this->metrics->increment('sms.sent.total');
$this->metrics->timing('sms.dispatch.duration', $duration);

if ($result) {
    $this->metrics->increment('sms.sent.success');
    $device->successCount++;
} else {
    $this->metrics->increment('sms.sent.failed');
    $device->failedCount++;
}

// Calculer le taux de réussite
if (($device->successCount + $device->failedCount) > 0) {
    $device->successRate = (int) (
        ($device->successCount / ($device->successCount + $device->failedCount)) * 100
    );
}
```

## 🔮 Roadmap

- [ ] Support des notifications push iOS
- [ ] Système de file d'attente intégré
- [ ] Support WebSocket pour les mises à jour en temps réel
- [ ] Dashboard de monitoring
- [ ] SDK JavaScript/TypeScript complémentaire
- [ ] Plugin Laravel/Symfony officiel

## 🤝 Contribution

Les contributions sont les bienvenues ! Voici comment participer :

1. **Signaler un bug** - [Ouvrir une issue](https://github.com/andydefer/autotext-sdk/issues)
2. **Proposer une fonctionnalité** - Discuter dans les issues
3. **Soumettre une PR** - Suivre les standards de code
4. **Améliorer la documentation** - Corrections et ajouts

### Standards de code
- Suivre PSR-12
- Ajouter des tests pour les nouvelles fonctionnalités
- Documenter les changements breaking
- Maintenir la rétrocompatibilité

## 📄 Licence

MIT License - Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

- **Documentation** : [github.com/andydefer/autotext-sdk](https://github.com/andydefer/autotext-sdk)
- **Issues** : [Signaler un problème](https://github.com/andydefer/autotext-sdk/issues)
- **Email** : andykanidimbu@gmail.com

---

**Note importante** : Ce SDK est conçu pour les environnements de production. Assurez-vous de :
- Tester en environnement de staging
- Mettre en place un système de monitoring
- Configurer les alertes d'erreur
- Sauvegarder régulièrement les tokens FCM
- Surveiller les quotas Firebase

## 📚 Ressources supplémentaires

- [Documentation Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging)
- [Guide des tokens FCM](https://firebase.google.com/docs/cloud-messaging/manage-tokens)
- [Meilleures pratiques FCM](https://firebase.google.com/docs/cloud-messaging/android/client)
- [Exemples d'implémentation](https://github.com/firebase/quickstart-android/tree/master/messaging)

**Version minimum** : PHP 8.1
**Dépendances** : GuzzleHTTP 7.0+
**License** : MIT
**Mainteneur** : Andy Kani