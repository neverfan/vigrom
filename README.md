<p align="center"><a href="https://vigromcorp.com/" target="_blank"><img src="https://sun9-52.userapi.com/c847221/v847221190/fe04a/7Z67nQ87er4.jpg" width="400"></a></p>

## Vigrom PHP Test

[см. требования](https://docs.google.com/document/d/1ecYbllmioscsLajXdholAswwCTZ8Jkt3Zx7ftmnfvrU/edit#heading=h.4purnitbyzyk)

Проект выполнен на базе [Laravel Sail](https://laravel.com/docs/9.x/sail)

### Развертывание проекта

- **make install**

### Запуск тестов

- **make testing**

### API
```GET:/api/wallet/balance/get/```
> Получить текущий баланс 

**Обязательные параметры:**

```yaml
  wallet_id:  [integer]
```

```POST:/api/wallet/balance/change/```
> Изменить текущий баланс

**Обязательные параметры:**

```yaml
  wallet_id:  [integer],
  transaction_type: [string],
  currency_amount: [float]
  currency_symbol: [string],
  transaction_reason: [string],
```
