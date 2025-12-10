# AndyDefer Autotext SDK

Un SDK PHP pour gérer l'envoi automatisé de SMS via des devices connectés et Firebase Cloud Messaging (FCM).
Le package est **framework-agnostic**, mais peut être utilisé facilement avec Laravel.

---

## Installation

Installez le package via Composer :

```bash
composer require andydefer/autotext-sdk
````

---

## Configuration Firebase

Créez un fichier JSON de configuration Firebase (obtenu depuis Firebase Console) et chargez-le en PHP :

```php
$config = json_decode(file_get_contents('/chemin/vers/fcm.json'), true);
$firebaseService = new \Andydefer\AutotextSdk\Services\FirebaseService($config);
```

Le tableau `$config` doit contenir :

* `project_id`
* `client_email`
* `private_key`
* `token_uri`

---

## DTOs disponibles

Le package utilise des **Data Transfer Objects (DTO)** pour rester indépendant du framework :

* **TextoDto** : représente un SMS.
* **AutoTextDeviceDto** : représente un device connecté.

Exemple :

```php
use Andydefer\AutotextSdk\Dtos\TextoDto;
use Andydefer\AutotextSdk\Enums\TextoStatus;

$texto = new TextoDto(
    id: 1,
    uuid: 'uuid-example',
    message: 'Bonjour',
    phoneNumber: '+33000000000',
    status: TextoStatus::PENDING,
    deviceId: 1,
    retryCount: 0,
    lastAttemptAt: null,
    createdAt: date('c'),
    updatedAt: date('c')
);
```

---

## Services principaux

### DeviceSmsDispatcher

Envoie un SMS à un device spécifique, qui se chargera de le dispatcher :

```php
use Andydefer\AutotextSdk\Services\DeviceSmsDispatcher;

$dispatcher = new DeviceSmsDispatcher($firebaseService);

$dispatcher->dispatch($texto, $deviceDto); // $deviceDto doit être en ligne
```

### FirebaseService

Service pour envoyer des messages FCM aux devices :

```php
$response = $firebaseService->sendSmsToDevice($deviceDto->fcmId, $texto);
```

---

## Enumérations

* `TextoStatus` : `PENDING`, `SUCCESS`, `FAILED`
* `AutoTextDeviceStatus` : `ONLINE`, `OFFLINE`
* `FcmActionType` : `SEND_SMS`, `INFO`, `PING`, `CONFIRM_SMS`

---

## Contribuer

1. Fork le repository
2. Crée ta branche : `git checkout -b feature/ma-fonctionnalite`
3. Commit tes changements : `git commit -am 'Ajout de ...'`
4. Push la branche : `git push origin feature/ma-fonctionnalite`
5. Ouvre une Pull Request

---

## Licence

MIT
---
