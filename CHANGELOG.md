# Changelog

Toutes les modifications notables de ce projet seront documentées ici.

## [1.0.0] - 2025-12-11

### Breaking Changes
- Suppression de `AutoTextDeviceDto` et `AutoTextDeviceStatus`.
- Remplacement par `DeviceDto` et `DeviceStatus` dans tout le code.
- Tous les tests, services et la documentation README ont été mis à jour pour utiliser les nouveaux noms.
- Les méthodes et propriétés des DTO peuvent avoir changé de noms ou de types.

### Added
- Nouvelle méthode `Device::createDto()` pour transformer un modèle Laravel en DTO (si applicable).
- Casts et typages améliorés dans `DeviceDto`.

### Fixed
- Correction du README et des exemples pour refléter la nouvelle structure.

