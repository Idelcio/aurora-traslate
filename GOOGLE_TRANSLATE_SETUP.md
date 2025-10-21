# 🌍 Configuração do Google Cloud Translation API

## 📋 Pré-requisitos

- Conta Google
- Projeto Google Cloud criado
- Faturamento habilitado no projeto

---

## 🚀 Passo a Passo para Configuração

### 1️⃣ Criar/Acessar Projeto no Google Cloud Console

1. Acesse: https://console.cloud.google.com/projectselector2
2. Se já tem projeto, selecione: **upheld-setting-463923-b1**
3. Se não, crie um novo projeto

### 2️⃣ Ativar a Translation API

1. Acesse: https://console.cloud.google.com/flows/enableapi?apiid=translate.googleapis.com
2. Selecione seu projeto: **upheld-setting-463923-b1**
3. Clique em **"Ativar"** (Enable)

### 3️⃣ Criar API Key

1. Acesse: https://console.cloud.google.com/apis/credentials
2. Clique em **"Create Credentials"** → **"API Key"**
3. **COPIE A CHAVE** que aparece (você só verá uma vez!)
4. Exemplo de chave: `AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxx`

### 4️⃣ Restringir a API Key (Segurança Importante!)

1. Na página de Credentials, clique no **nome da chave** que você acabou de criar
2. Em **"API restrictions"**:
   - Selecione **"Restrict key"**
   - Marque: **"Cloud Translation API"**
3. Clique em **"Save"**

### 5️⃣ Configurar no Laravel

1. Abra o arquivo `.env` na raiz do projeto
2. Adicione as seguintes linhas:

```env
GOOGLE_TRANSLATE_API_KEY=SUA_API_KEY_AQUI
GOOGLE_TRANSLATE_PROJECT_ID=upheld-setting-463923-b1
```

3. Substitua `SUA_API_KEY_AQUI` pela chave que você copiou no passo 3

**Exemplo:**
```env
GOOGLE_TRANSLATE_API_KEY=AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxx
GOOGLE_TRANSLATE_PROJECT_ID=upheld-setting-463923-b1
```

### 6️⃣ Testar a Configuração

Execute no terminal:

```bash
php artisan tinker
```

Depois execute:

```php
$service = new \App\Services\GoogleTranslateService();
$result = $service->translate('Hello World', 'pt');
dd($result);
```

Se tudo estiver correto, você verá:

```php
array:3 [
  "success" => true
  "translatedText" => "Olá Mundo"
  "detectedSourceLanguage" => "en"
]
```

---

## 💰 Informações sobre Custos

### Créditos Grátis
- **$300** em créditos grátis para novos usuários
- Válido por **90 dias**

### Preços após créditos
- **$20 por 1 milhão de caracteres** traduzidos
- Exemplo: 100.000 caracteres = **$2**

### Ver uso atual
https://console.cloud.google.com/apis/api/translate.googleapis.com/quotas

---

## 🔧 Como Usar no Código

### Exemplo 1: Traduzir texto simples

```php
use App\Services\GoogleTranslateService;

$translator = new GoogleTranslateService();

$result = $translator->translate(
    'Hello, how are you?',  // Texto
    'pt',                    // Idioma destino
    'en'                     // Idioma origem (opcional)
);

if ($result['success']) {
    echo $result['translatedText']; // "Olá, como você está?"
}
```

### Exemplo 2: Traduzir múltiplos textos

```php
$texts = [
    'Hello',
    'Good morning',
    'Thank you'
];

$result = $translator->translateBatch($texts, 'pt', 'en');

foreach ($result['translations'] as $translation) {
    echo $translation['translatedText'] . "\n";
}
// Output:
// Olá
// Bom dia
// Obrigado
```

### Exemplo 3: Detectar idioma

```php
$result = $translator->detectLanguage('Bonjour tout le monde');

if ($result['success']) {
    echo $result['language'];   // "fr"
    echo $result['confidence']; // 0.98
}
```

### Exemplo 4: Listar idiomas suportados

```php
$result = $translator->getSupportedLanguages('pt');

foreach ($result['languages'] as $lang) {
    echo $lang['language'] . ' - ' . $lang['name'] . "\n";
}
// Output:
// en - Inglês
// es - Espanhol
// fr - Francês
// ...
```

---

## 📚 Códigos de Idioma Suportados

| Código | Idioma |
|--------|--------|
| `pt` | Português |
| `en` | Inglês |
| `es` | Espanhol |
| `fr` | Francês |
| `de` | Alemão |
| `it` | Italiano |
| `ja` | Japonês |
| `ko` | Coreano |
| `zh` | Chinês |
| `ar` | Árabe |

**Lista completa:** https://cloud.google.com/translate/docs/languages

---

## ⚠️ Troubleshooting

### Erro: "API key not valid"
- Verifique se copiou a chave corretamente
- Verifique se a Translation API está ativada
- Verifique se a chave tem permissão para Translation API

### Erro: "Project ID not found"
- Verifique se o PROJECT_ID está correto no `.env`
- Use: `upheld-setting-463923-b1`

### Erro: "Billing not enabled"
- Acesse: https://console.cloud.google.com/billing
- Vincule uma conta de faturamento ao projeto

---

## 🔒 Segurança

**NUNCA compartilhe sua API KEY!**

✅ **Boas práticas:**
- Adicione `.env` ao `.gitignore`
- Restrinja a API key apenas para Translation API
- Monitore o uso regularmente
- Rotacione as chaves periodicamente

---

## 📞 Suporte

- **Documentação oficial:** https://cloud.google.com/translate/docs
- **Console Google Cloud:** https://console.cloud.google.com
- **Pricing:** https://cloud.google.com/translate/pricing

---

**Projeto:** upheld-setting-463923-b1
**Número do Projeto:** 799464065671
