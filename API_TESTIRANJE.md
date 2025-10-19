# 🧪 API TESTIRANJE - Real Estate Aplikacija

## 🚀 KAKO TESTIRATI API ENDPOINT-E

### 1. POKRETANJE SERVERA
```bash
php artisan serve
```
Server će biti dostupan na: `http://localhost:8000`

---

## 📋 OSNOVNI CRUD TESTOVI

### 1. GET - Sve nekretnine
```bash
curl -X GET "http://localhost:8000/api/properties" \
     -H "Accept: application/json"
```

### 2. GET - Jedna nekretnina
```bash
curl -X GET "http://localhost:8000/api/properties/1" \
     -H "Accept: application/json"
```

### 3. POST - Nova nekretnina
```bash
curl -X POST "http://localhost:8000/api/properties" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "property_name": "Test API Property",
       "property_code": "API001",
       "ptype_id": 1,
       "agent_id": 1,
       "property_status": "rent",
       "lowest_price": 50000,
       "max_price": 75000,
       "short_descp": "Test property created via API",
       "bedrooms": "2",
       "bathrooms": "1",
       "address": "Test Address 123",
       "city": "Beograd"
     }'
```

### 4. PUT - Ažuriranje nekretnine
```bash
curl -X PUT "http://localhost:8000/api/properties/1" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "property_name": "Updated Property Name",
       "lowest_price": 60000
     }'
```

### 5. DELETE - Brisanje nekretnine
```bash
curl -X DELETE "http://localhost:8000/api/properties/1" \
     -H "Accept: application/json"
```

---

## 🔗 UGNJEŽDENE RUTE TESTOVI

### 1. Nekretnine određenog korisnika
```bash
curl -X GET "http://localhost:8000/api/users/1/properties" \
     -H "Accept: application/json"
```

### 2. Slike određene nekretnine
```bash
curl -X GET "http://localhost:8000/api/properties/1/images" \
     -H "Accept: application/json"
```

---

## 🌐 JAVNI VEB SERVISI TESTOVI

### 1. Geolokacija nekretnine (OpenStreetMap API)
```bash
curl -X GET "http://localhost:8000/api/properties/1/location" \
     -H "Accept: application/json"
```

### 2. Konverzija valute (ExchangeRate API)
```bash
# Konverzija u EUR
curl -X GET "http://localhost:8000/api/properties/1/convert/EUR" \
     -H "Accept: application/json"

# Konverzija u USD
curl -X GET "http://localhost:8000/api/properties/1/convert/USD" \
     -H "Accept: application/json"
```

---

## 📊 DODATNI ENDPOINT-I

### 1. Tipovi nekretnina
```bash
curl -X GET "http://localhost:8000/api/property-types" \
     -H "Accept: application/json"
```

### 2. Države/Regioni
```bash
curl -X GET "http://localhost:8000/api/states" \
     -H "Accept: application/json"
```

---

## 🧪 TESTIRANJE PREKO POSTMAN-a

### Kreiranje kolekcije u Postman-u:

1. **Kreiranje nove kolekcije:** `Real Estate API`

2. **Dodavanje zahteva:**

#### GET Properties
- Method: `GET`
- URL: `{{base_url}}/api/properties`
- Headers: `Accept: application/json`

#### POST Property
- Method: `POST`
- URL: `{{base_url}}/api/properties`
- Headers: 
  - `Content-Type: application/json`
  - `Accept: application/json`
- Body (raw JSON):
```json
{
  "property_name": "Postman Test Property",
  "property_code": "POST001",
  "ptype_id": 1,
  "agent_id": 1,
  "property_status": "sale",
  "lowest_price": 100000,
  "max_price": 150000,
  "short_descp": "Created from Postman",
  "bedrooms": "3",
  "bathrooms": "2",
  "address": "Postman Street 456",
  "city": "Novi Sad"
}
```

3. **Environment variables:**
- `base_url`: `http://localhost:8000`

---

## ✅ OČEKIVANI REZULTATI

### Uspešan GET zahtev:
```json
{
  "success": true,
  "message": "Properties retrieved successfully",
  "count": 5,
  "data": [...]
}
```

### Uspešan POST zahtev:
```json
{
  "success": true,
  "message": "Property created successfully",
  "data": {
    "id": 1,
    "property_name": "Test API Property",
    ...
  }
}
```

### Greška 404:
```json
{
  "success": false,
  "message": "Property not found"
}
```

### Validation greška:
```json
{
  "success": false,
  "message": "Validation Error",
  "errors": {
    "property_name": ["The property name field is required."]
  }
}
```

---

## 🚨 TROUBLESHOOTING

### Ako API ne radi:

1. **Proveri da li je server pokrenut:**
```bash
php artisan serve
```

2. **Proveri rute:**
```bash
php artisan route:list --path=api
```

3. **Proveri logove:**
```bash
tail -f storage/logs/laravel.log
```

4. **Isprazni cache:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## 📈 PERFORMANCE TESTIRANJE

### Apache Bench test:
```bash
# Test GET endpoint-a
ab -n 100 -c 10 http://localhost:8000/api/properties

# Test sa JSON header-om
ab -n 100 -c 10 -H "Accept: application/json" http://localhost:8000/api/properties
```

---

**🎯 CILJ:** Svi endpoint-i treba da vraćaju JSON odgovore sa odgovarajućim HTTP status kodovima (200, 201, 404, 400, 500).