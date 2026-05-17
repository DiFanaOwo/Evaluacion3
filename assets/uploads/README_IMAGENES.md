# 📸 INSTRUCCIONES - CÓMO SUBIR TUS IMÁGENES

## 📁 Estructura de Carpetas para Imágenes

```
assets/
└── uploads/
    ├── logo-ingeniosos.png          ← TU LOGO AQUÍ
    ├── logo-ingeniosos.jpg          ← O EN JPG
    └── productos/
        ├── bebes-placeholder.jpg      ← Categoría 0-6 meses
        ├── educativos-placeholder.jpg ← Categoría 6-12 meses
        ├── aventuras-placeholder.jpg  ← Categoría 1-2 años
        ├── naturaleza-placeholder.jpg ← Categoría 3-4 años
        ├── interactivos-placeholder.jpg ← Categoría Más de 5 años
        └── ver-todo-placeholder.jpg   ← Categoría Ver Todo
```

---

## 🎯 PASO 1: SUBIR TU LOGO

### Ubicación del archivo:
```
c:\xampp\htdocs\tiendalibros\Evaluacion3\assets\uploads\
```

### Pasos:
1. Dirígete a esa carpeta en tu explorador de archivos
2. Pega tu archivo de logo
3. **IMPORTANTE**: Renómbralo como `logo-ingeniosos.png` (o `.jpg`)

### Formatos soportados:
- PNG (recomendado - soporta transparencia)
- JPG
- GIF

### Tamaño recomendado:
- Ancho: 60-100 px
- Alto: 60-100 px
- Proporción: 1:1 (cuadrado)

---

## 📷 PASO 2: SUBIR IMÁGENES DE CATEGORÍAS

### Ubicación de la carpeta:
```
c:\xampp\htdocs\tiendalibros\Evaluacion3\assets\uploads\productos\
```

### Imágenes a subir:

| Archivo | Categoría | Tamaño recomendado |
|---------|-----------|-------------------|
| `bebes-placeholder.jpg` | 0-6 meses | 200x220 px |
| `educativos-placeholder.jpg` | 6-12 meses | 200x220 px |
| `aventuras-placeholder.jpg` | 1-2 años | 200x220 px |
| `naturaleza-placeholder.jpg` | 3-4 años | 200x220 px |
| `interactivos-placeholder.jpg` | Más de 5 años | 400x220 px (MÁS GRANDE) |
| `ver-todo-placeholder.jpg` | Ver Todo | 200x220 px |

### Pasos:
1. Prepara tus imágenes de productos/categorías
2. Redimensiónalas al tamaño recomendado
3. Copia los archivos a la carpeta `productos/`
4. **IMPORTANTE**: Usa EXACTAMENTE los nombres de arriba

---

## 🎨 CÓMO NOMBRAR LOS ARCHIVOS CORRECTAMENTE

❌ **INCORRECTO**:
```
- Bebé.jpg
- EDUCATIVOS DIDACTICOS.jpg
- foto_aventura.jpg
- img_naturales.png
```

✅ **CORRECTO**:
```
- bebes-placeholder.jpg
- educativos-placeholder.jpg
- aventuras-placeholder.jpg
- naturaleza-placeholder.jpg
- interactivos-placeholder.jpg
- ver-todo-placeholder.jpg
```

---

## 🎬 RESULTADO ESPERADO

Después de subir tus imágenes, la página mostrará:

```
┌─────────────────────────────────────┐
│      [TU LOGO INGENIOSOS]            │
│    Navbar | Search | User Icons      │
├─────────────────────────────────────┤
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌────┐ │
│  │      │ │      │ │      │ │    │ │
│  │ FOTO │ │ FOTO │ │ FOTO │ │IMG │ │
│  │  0-6 │ │ 6-12 │ │  1-2 │ │3-4 │ │
│  │meses │ │meses │ │ años │ │años│ │
│  └──────┘ └──────┘ └──────┘ └────┘ │
│  ┌──────────────────┐ ┌──────┐     │
│  │                  │ │      │     │
│  │    FOTO +5       │ │ VER  │     │
│  │    años (grande) │ │ TODO │     │
│  └──────────────────┘ └──────┘     │
└─────────────────────────────────────┘

AL PASAR EL MOUSE:
┌─────────────────────────────────────┐
│                                     │
│           ┏━━━━━━━━━━━┓             │
│           ┃           ┃             │
│           ┃  FOTO     ┃  ← CRECE   │
│           ┃  GRANDE   ┃             │
│           ┃           ┃             │
│           ┗━━━━━━━━━━━┛             │
│                                     │
└─────────────────────────────────────┘
```

---

## 🔧 SI NECESITAS CAMBIAR LOS NOMBRES

Si deseas usar otros nombres de archivos, edita el archivo:

```
c:\xampp\htdocs\tiendalibros\Evaluacion3\cliente\productos.php
```

Busca estas líneas y cambia los nombres:

```php
<img src="../assets/uploads/productos/bebes-placeholder.jpg" alt="...">
<img src="../assets/uploads/productos/educativos-placeholder.jpg" alt="...">
<!-- etc -->
```

---

## 🎬 PARA EL LOGO

En el archivo `productos.php`, busca:

```php
<img src="../assets/uploads/logo-ingeniosos.png" alt="Logo Ingeniosos" class="logo-img-real">
```

Si tu logo está en JPG, cambia `.png` por `.jpg`

---

## 📝 CHECKLIST

- [ ] Carpeta `assets/uploads/` existe
- [ ] Carpeta `assets/uploads/productos/` existe
- [ ] Logo subido en `assets/uploads/`
- [ ] 6 imágenes de categorías subidas en `assets/uploads/productos/`
- [ ] Todos los archivos con nombres exactos
- [ ] Tamaños correctos (200x220 px para categorías normales, 400x220 para "Más de 5 años")

---

## 💡 NOTAS IMPORTANTES

1. **Los nombres son sensibles a mayúsculas/minúsculas**
   - `Bebes-placeholder.jpg` ❌ (mayúscula al inicio)
   - `bebes-placeholder.jpg` ✅ (todo minúscula)

2. **No uses espacios en los nombres**
   - `bebés placeholder.jpg` ❌
   - `bebes-placeholder.jpg` ✅

3. **Usa guiones para separar palabras**
   - `bebes_placeholder.jpg` (con guion bajo) funciona pero:
   - `bebes-placeholder.jpg` (con guion) es mejor

4. **Formato de imagen recomendado**: JPG
   - Menor tamaño de archivo
   - Compatible con todos los navegadores
   - Ideal para fotos

---

## 🚨 ERRORES COMUNES

### ❌ Error: "Imagen no carga"

**Causa**: Nombre de archivo incorrecto

**Solución**: Verifica que el nombre sea exacto:
```
✅ bebes-placeholder.jpg
❌ Bebes placeholder.jpg
❌ bebes.jpg
❌ bebes_placeholder.jpg
```

### ❌ Error: "Carpeta no encontrada"

**Causa**: Carpeta `uploads/` no existe

**Solución**: Créala manualmente:
1. Ve a `assets/`
2. Crea carpeta `uploads`
3. Dentro crea carpeta `productos`

### ❌ Error: "Logo no aparece"

**Causa**: Logo no en la ruta correcta

**Solución**: 
- Archivo debe estar en: `assets/uploads/logo-ingeniosos.png`
- Nombre exacto: `logo-ingeniosos`

---

## ✅ TODO LISTO

Una vez subas todas las imágenes:

1. Abre tu navegador
2. Ve a: `http://localhost/tiendalibros/Evaluacion3/cliente/productos.php`
3. ¡Verás tu logo y tus imágenes!

**¿Necesitas ayuda?** Verifica los pasos anteriores.

---

*Última actualización: Mayo 16, 2026*
