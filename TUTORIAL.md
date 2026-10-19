# Mafunzo: Student Web App (PHP + MySQL + AdminLTE) kwa Beginners

Mafunzo haya yanaeleza **hatua kwa hatua** code ya app hii: inafanya nini, kila faili ina kazi gani, na kwa nini imeandikwa hivyo. Hakuna kitu kinachodhaniwa — tunaanza kutoka sifuri.

---

## 1. Tunajenga nini?

App ndogo ya **kusimamia wanafunzi** (Student Management). Inaweza:

| Kitendo | Jina la kitaalamu | Faili |
|---|---|---|
| **C**reate – kuongeza mwanafunzi | CRUD | `students/create.php` |
| **R**ead – kuona orodha | CRUD | `students/read.php` |
| **U**pdate – kuhariri | CRUD | `students/update.php` |
| **D**elete – kufuta | CRUD | `students/delete.php` |

Pia kuna **Dashboard** inayoonyesha muhtasari (takwimu, tabs, progress bars, n.k).

> **CRUD** = Create, Read, Update, Delete. Karibu kila app ya database duniani inatumia mambo haya manne.

---

## 2. Vitu unavyohitaji

1. **XAMPP** (inakuletea Apache + MySQL + PHP).
2. Code editor (VS Code n.k).
3. Browser.

### Jinsi ya kuendesha
1. Fungua **XAMPP Control Panel** → bonyeza **Start** kwa **Apache** na **MySQL**.
2. Weka folda ya project hapa: `C:\xampp\htdocs\lesson`
3. Fungua browser: `http://localhost/lesson/`

> `htdocs` ndiyo folda ambayo Apache inaitumia kuonyesha tovuti. Chochote ulichoweka humo kinaweza kufunguliwa kupitia `http://localhost/`.

---

## 3. Muundo wa folda (Project Structure)

```
lesson/
├── index.php            ← Mlango wa kuingia (redirect kwenda dashboard)
├── dashboard.php        ← Ukurasa mkuu
├── components.php       ← Fomu inayotumika tena (create & update)
├── config/
│   └── database.php     ← Muunganisho wa database
├── include/             ← Sehemu za ukurasa zinazojirudia
│   ├── header.php       ← Juu ya ukurasa (HTML head, CSS)
│   ├── navbar.php       ← Menu ya juu
│   ├── sidebar.php      ← Menu ya pembeni
│   └── footer.php       ← Chini ya ukurasa (JS)
├── students/
│   ├── create.php
│   ├── read.php
│   ├── update.php
│   └── delete.php
└── adminlte/            ← Design (CSS/JS) iliyotengenezwa tayari
```

**Wazo kuu:** badala ya kuandika navbar/sidebar kwenye kila ukurasa, tunaziandika **mara moja** kwenye `include/` kisha tunazi-*include* kila mahali. Hii inaitwa **kutumia tena code (reusability)**.

---

## 4. Misingi ya PHP unayohitaji kujua kwanza

### 4.1 Tag za PHP
```php
<?php
  // code ya PHP iko hapa
?>
```
PHP inafanya kazi **kwenye server**; browser inapokea HTML tu.

### 4.2 Variable
```php
$jina = "John";      // variable huanza na $
echo $jina;          // inachapisha: John
```

### 4.3 Short echo `<?= ... ?>`
```php
<td><?= $jina ?></td>
```
Ni njia fupi ya `<?php echo $jina; ?>`. Utaiona sana kwenye project hii.

### 4.4 Array
```php
$student = ['name' => 'John', 'course' => 'IT'];
echo $student['name'];   // John
```

### 4.5 Loop (`foreach`)
```php
foreach ($students as $s) {
    echo $s['name'];
}
```
Inapitia kila mwanafunzi mmoja baada ya mwingine.

### 4.6 `require`
```php
require "config/database.php";
```
Inaingiza faili lingine kwenye ukurasa huu. Faili likikosekana → app inasimama na kuonyesha error.

---

## 5. Hatua ya 1: Database (`config/database.php`)

Database ni mahali data inahifadhiwa kwa kudumu. Tunatumia **MySQL**, na tunawasiliana nayo kupitia **PDO** (njia salama ya PHP).

```php
$host     = "localhost";        // MySQL iko kwenye kompyuta hii
$dbname   = "adminlte_demo";    // jina la database
$username = "root";             // user wa kawaida wa XAMPP
$password = "";                 // XAMPP haina password kwa default
```

### Kuunganisha
```php
$pdo = new PDO("mysql:host=$host", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```
- `new PDO(...)` → tunatengeneza muunganisho na kuuhifadhi kwenye `$pdo`.
- `ERRMODE_EXCEPTION` → kukiwa na kosa la SQL, PHP inalionyesha wazi (inasaidia kujifunza).

### try / catch
```php
try {
    // jaribu kuunganisha...
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
```
Ikishindikana (mfano MySQL imezimwa), tunaonyesha ujumbe badala ya ukurasa kuvunjika. `die()` inasimamisha app.

### Kutengeneza database na table kiotomatiki
```php
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
$pdo->exec("USE `$dbname`");
$pdo->exec("CREATE TABLE IF NOT EXISTS students (...)");
```
`IF NOT EXISTS` = "tengeneza kama haipo tayari". Kwa hiyo unaweza kufungua app mara nyingi bila error.

### Table ya `students`
| Column | Aina | Maana |
|---|---|---|
| `id` | `INT AUTO_INCREMENT PRIMARY KEY` | Namba ya kipekee, inaongezeka yenyewe |
| `name` | `VARCHAR(100)` | Jina (hadi herufi 100) |
| `email` | `VARCHAR(150)` | Barua pepe |
| `course` | `VARCHAR(100)` | Kozi |
| `status` | `ENUM('Active','Inactive')` | Hali — thamani mbili tu zinaruhusiwa |
| `created_at` | `TIMESTAMP` | Tarehe/saa ya kuongezwa (otomatiki) |

### `BASE_URL`
```php
define('BASE_URL', '/lesson/');
```
Njia ya msingi ya tovuti. Tunaitumia kwenye links na CSS ili zifanye kazi kutoka folda yoyote (kuu au `students/`).

---

## 6. Hatua ya 2: Layout inayojirudia (`include/`)

### `header.php`
Inaanza HTML: `<head>`, CSS za Bootstrap/AdminLTE, kisha inaingiza navbar na sidebar:
```php
<link rel="stylesheet" href="<?= BASE_URL ?>adminlte/dist/css/adminlte.min.css">
...
<?php require __DIR__ . '/navbar.php'; ?>
<?php require __DIR__ . '/sidebar.php'; ?>
<main class="app-main">
```
> `__DIR__` = folda ya faili la sasa. Inahakikisha `require` inapata faili hata ukiitwa kutoka folda nyingine.

### `footer.php`
Inafunga tags zilizofunguliwa na header, na kupakia JavaScript:
```php
</main>
<footer class="app-footer">...</footer>
<script src=".../bootstrap.bundle.min.js"></script>
<script src=".../adminlte.min.js"></script>
```
**JavaScript inawekwa chini** ili ukurasa uonekane haraka kwanza.

### Mfumo wa kila ukurasa
```php
<?php
require "config/database.php";   // 1. unganisha DB
require "include/header.php";    // 2. sehemu ya juu
?>
   ... maudhui ya ukurasa huu ...
<?php require "include/footer.php"; ?>   <!-- 3. sehemu ya chini -->
```
Kila ukurasa unafuata muundo huu: **juu → maudhui → chini**.

---

## 7. Hatua ya 3: Read — kuona wanafunzi (`students/read.php`)

```php
$students = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
```
Kuvunja:
- `SELECT * FROM students` → "nipe safu zote za table students"
- `ORDER BY id DESC` → mpya zaidi juu
- `fetchAll(PDO::FETCH_ASSOC)` → badilisha matokeo kuwa **array** ya PHP yenye majina ya columns

Kisha tunaonyesha kwenye table:
```php
<?php foreach ($students as $s): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        ...
    </tr>
<?php endforeach; ?>
```

### `htmlspecialchars()` — MUHIMU kwa usalama
Mtu akiandika jina kama `<script>alert('hack')</script>`, bila `htmlspecialchars` code hiyo ingefanya kazi kwenye browser ya wengine (shambulio la **XSS**). Hii function inabadilisha `<` kuwa maandishi salama. **Daima itumie unapoonyesha data ya mtumiaji.**

### Badge ya status
```php
<span class="badge text-bg-<?= $s['status'] === 'Active' ? 'success' : 'secondary' ?>">
```
Hii inaitwa **ternary operator**: `sharti ? kama_kweli : kama_si_kweli`. Active → kijani, vinginevyo → kijivu.

---

## 8. Hatua ya 4: Fomu inayotumika tena (`components.php`)

Create na Update zinahitaji fomu **ileile**. Tunaiandika mara moja kama **function**:

```php
function student_form(array $s = [], string $button = "Save") { ?>
    <form method="post">
        <input type="text" name="name" value="<?= htmlspecialchars($s['name'] ?? '') ?>">
        ...
    </form>
<?php }
```

Maelezo:
- `array $s = []` → data ya mwanafunzi. Kwa **create** ni tupu, kwa **update** ina data ya zamani.
- `$s['name'] ?? ''` → "tumia jina, na kama halipo tumia tupu". `??` inaitwa *null coalescing*.
- `method="post"` → data inatumwa kwa siri (haionekani kwenye URL).
- `name="name"` → jina hili ndilo linalotumika kuipata data kwenye PHP: `$_POST['name']`.
- `required` → browser inakataa fomu tupu.

---

## 9. Hatua ya 5: Create (`students/create.php`)

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO students (name, email, course, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([trim($_POST['name']), trim($_POST['email']), trim($_POST['course']), $_POST['status']]);
    header("Location: read.php");
    exit;
}
```

Hatua kwa hatua:
1. `REQUEST_METHOD === 'POST'` → ukurasa unafunguliwa kwa **GET** (kuona fomu) au **POST** (fomu imetumwa). Tunahifadhi tu pale fomu inapotumwa.
2. `$_POST['name']` → thamani aliyoandika mtumiaji.
3. `trim()` → inaondoa nafasi tupu mwanzo/mwisho.
4. `prepare` + `?` + `execute` → **Prepared Statements**.
5. `header("Location: read.php")` → mpeleke kwenye orodha baada ya kuhifadhi.
6. `exit` → simamisha code (lazima baada ya redirect).

### Kwa nini `?` na si kuweka data moja kwa moja?
**Mbaya (hatari):**
```php
$pdo->query("INSERT ... VALUES ('$_POST[name]')");   // ❌ SQL Injection
```
**Sahihi:**
```php
$stmt = $pdo->prepare("INSERT ... VALUES (?)");      // ✅
$stmt->execute([$_POST['name']]);
```
Prepared statements zinatenganisha **amri ya SQL** na **data**, hivyo hacker hawezi kuingiza amri zake. **Hii ni kanuni ya dhahabu: kamwe usiunganishe input ya mtumiaji moja kwa moja kwenye SQL.**

---

## 10. Hatua ya 6: Update (`students/update.php`)

```php
$id = (int)($_GET['id'] ?? 0);
```
- `$_GET['id']` → id kutoka kwenye URL: `update.php?id=3`
- `(int)` → ilazimishe iwe namba (usalama).

Tunatafuta mwanafunzi:
```php
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) { header("Location: read.php"); exit; }
```
- `fetch` → safu **moja** (si `fetchAll`).
- Hakupatikana → rudi kwenye orodha.

Kisha fomu inapotumwa:
```php
UPDATE students SET name=?, email=?, course=?, status=? WHERE id=?
```
> ⚠️ **`WHERE id=?` ni muhimu sana!** Ukiisahau, UPDATE itabadilisha **wanafunzi WOTE**.

Fomu inaonyeshwa na data ya zamani: `student_form($student, "Update")`.

---

## 11. Hatua ya 7: Delete (`students/delete.php`)

```php
$id = (int)($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
header("Location: read.php");
exit;
```
Kwenye `read.php`, kitufe cha kufuta kina uthibitisho:
```html
<a href="delete.php?id=..." onclick="return confirm('Delete this student?')">
```
`confirm()` ya JavaScript inauliza "Una uhakika?". Ukibonyeza Cancel, link haifuatwi.

> ⚠️ Tena `WHERE` ni muhimu — bila hiyo unafuta table nzima!

---

## 12. Hatua ya 8: Dashboard (`dashboard.php`)

Dashboard inatumia vipengele (components) vya Bootstrap/AdminLTE. Hii hapa orodha na maana yake:

| Component | Inafanya nini | Mfano kwenye code |
|---|---|---|
| **Breadcrumb** | Inaonyesha uko wapi kwenye tovuti | `<ol class="breadcrumb">` |
| **Callout** | Kisanduku cha ujumbe/taarifa | `<div class="callout ...">` |
| **Small box** | Kadi ya takwimu (jumla, active...) | `<div class="small-box text-bg-primary">` |
| **Badge** | Lebo ndogo ya rangi | `<span class="badge text-bg-success">` |
| **Progress bar** | Mstari wa asilimia | `<div class="progress-bar" style="width: 60%">` |
| **Tabs** | Vichupo vya kubadilisha maudhui | `data-bs-toggle="tab"` |
| **Table** | Jedwali la data | `<table class="table table-striped">` |
| **Pagination** | Kurasa 1, 2, 3... | `<ul class="pagination">` |
| **Input group** | Kisanduku cha kuingiza chenye icon | `<div class="input-group">` |
| **Dropdown** | Menu inayofunguka | `data-bs-toggle="dropdown"` |
| **Modal** | Dirisha linalojitokeza | `data-bs-toggle="modal"` |
| **Buttons** | Vitufe | `class="btn btn-primary"` |

### 12.1 Kuandaa takwimu kwa PHP
```php
$all      = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$total    = count($all);
$active   = count(array_filter($all, fn($s) => $s['status'] === 'Active'));
$inactive = $total - $active;
$pct      = $total ? round($active / $total * 100) : 0;
```
- `count()` → idadi ya vipengele kwenye array.
- `array_filter(..., fn($s) => ...)` → chukua wale tu wanaokidhi sharti. `fn($s) => ...` ni *arrow function* (function fupi).
- `$pct` → asilimia. `$total ? ... : 0` huzuia **kugawanya kwa sifuri** (kukiwa hakuna wanafunzi).

### 12.2 Progress bar
```php
<div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
```
PHP inaweka asilimia halisi kwenye `width`. Kama `$pct = 67` → bar inajaa 67%.

### 12.3 Idadi kwa kila course
```php
$courses = [];
foreach ($all as $s) {
    $courses[$s['course']] = ($courses[$s['course']] ?? 0) + 1;
}
arsort($courses);   // panga kutoka nyingi hadi chache
```
Kwa kila mwanafunzi, tunaongeza 1 kwenye hesabu ya course yake.

### 12.4 Pagination (kurasa)
```php
$perPage = 5;                                   // wanafunzi 5 kwa ukurasa
$page    = max(1, (int)($_GET['page'] ?? 1));   // ukurasa wa sasa (angalau 1)
$pages   = max(1, (int)ceil($total / $perPage));// jumla ya kurasa
$paged   = array_slice($all, ($page - 1) * $perPage, $perPage);
```
Mfano wanafunzi 12 → kurasa 3. Ukurasa wa 2 → `array_slice($all, 5, 5)` → wanafunzi wa 6 hadi 10.
Links ni `?page=2`, `?page=3`... na PHP inasoma `$_GET['page']`.

### 12.5 Function `student_rows()`
Tunaitumia mara 3 (tab ya All, Active, Inactive) badala ya kurudia code ile ile — **DRY (Don't Repeat Yourself)**.

### 12.6 Tabs
```html
<button data-bs-toggle="tab" data-bs-target="#tab-all">All</button>
...
<div class="tab-pane" id="tab-all">...</div>
```
`data-bs-target="#tab-all"` inaunganisha kitufe na paneli yenye `id="tab-all"`. Bootstrap JS inafanya kazi yote — hauandiki JavaScript.

### 12.7 Modal (Quick Add)
Kitufe:
```html
<button data-bs-toggle="modal" data-bs-target="#addStudentModal">Quick Add</button>
```
Modal yenyewe ina `id="addStudentModal"` na fomu inayotuma kwa `action="students/create.php"`, kwa hiyo hatuhitaji kuandika code mpya ya kuhifadhi.

### 12.8 Kichujio cha utafutaji (JavaScript)
```javascript
document.getElementById('studentSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('table.searchable tbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
```
Kwa maneno rahisi: *kila mtu anapoandika kwenye kisanduku, pitia kila safu ya table; kama maandishi yake yanaingiza alichoandika, ionyeshe, vinginevyo ifiche.* Inafanya kazi kwenye browser, bila kupakia ukurasa upya.

---

## 13. Mtiririko mzima (Flow)

```
Mtumiaji → http://localhost/lesson/
   │
   ▼
index.php ── redirect ──► dashboard.php
                              │
                              ├─ require database.php  (unganisha MySQL)
                              ├─ require header.php    (navbar + sidebar)
                              ├─ PHP inasoma wanafunzi kutoka DB
                              ├─ HTML inaonyesha takwimu/tabs/tables
                              └─ require footer.php    (JS)

Sidebar → Students → read.php → [Edit] → update.php?id=3 → (POST) → read.php
                              → [Delete] → delete.php?id=3 → read.php
        → Add Student → create.php → (POST) → read.php
```

---

## 14. Makosa ya kawaida (na suluhisho)

| Tatizo | Sababu | Suluhisho |
|---|---|---|
| `Connection failed` | MySQL haijawashwa | Start MySQL kwenye XAMPP |
| Ukurasa mweupe | Kosa la PHP lililofichwa | Angalia `C:\xampp\apache\logs\error.log` |
| CSS haionekani | `BASE_URL` si sahihi | Hakikisha folda ni `lesson` na `BASE_URL = '/lesson/'` |
| `Undefined array key` | Jina la column/input halilingani | Linganisha `name="..."` kwenye fomu na `$_POST['...']` |
| Faili la PHP linaonyesha code badala ya kufanya kazi | Umefungua kwa `file:///` | Tumia `http://localhost/...` |
| `Failed to open stream` | Njia ya `require` si sahihi | Tumia `__DIR__ . "/..."` |

---

## 15. Mazoezi kwa Wanafunzi (Homework)

1. **Rahisi:** Ongeza column ya `phone` kwenye table na fomu zote.
2. **Rahisi:** Badilisha rangi ya small-box ya "Inactive" kuwa nyekundu (`text-bg-danger`).
3. **Kati:** Ongeza **search** kwenye `read.php` kwa kutumia `WHERE name LIKE ?`.
4. **Kati:** Kwenye `create.php`, kagua kama email tayari ipo kabla ya kuhifadhi.
5. **Ngumu:** Ongeza **login** ya admin kwa kutumia `session_start()` na `password_hash()`.
6. **Ngumu:** Badilisha `delete.php` itumie **POST** badala ya GET (salama zaidi).

---

## 16. Muhtasari (Cheat Sheet)

| Dhana | Kumbuka |
|---|---|
| `require` | Ingiza faili lingine |
| `$pdo->prepare()` + `execute()` | Daima tumia kwa SQL yenye input ya mtumiaji |
| `htmlspecialchars()` | Daima tumia unapoonyesha data ya mtumiaji |
| `$_POST` | Data kutoka fomu (method="post") |
| `$_GET` | Data kutoka URL (`?id=3`) |
| `header("Location: ...")` + `exit` | Peleka mtumiaji ukurasa mwingine |
| `WHERE` | Usisahau kwenye UPDATE na DELETE |
| `include/` | Kutumia tena sehemu za ukurasa |

**Hongera!** Ukielewa mambo haya, umejifunza msingi wa karibu kila web app ya PHP. 🎉

## 12. Kazi ya individual: components na pages zilizoongezwa

Kazi hii imetekeleza components mbili kati ya ulizopewa:

1. **Charts** — ukurasa `analytics.php` una bar chart ya idadi ya wanafunzi kwa kila course na progress charts za Active/Inactive. Takwimu zinatoka MySQL, si namba zilizowekwa kwa mkono.
2. **Select / Dropdown** — `analytics.php` ina dropdown mbili za kuchuja wanafunzi kwa status na course. `courses.php` pia ina dropdown za department na duration kwenye fomu ya kuongeza course.

### Kurasa zilizoongezwa
- `analytics.php` — charts, summary cards, dropdown filters, na table ya matokeo yaliyochujwa.
- `courses.php` — fomu ya kuongeza course na table mpya ya courses.

### Table mpya ya database
`courses` ina columns hizi:
- `id` — namba ya kipekee inayoongezeka yenyewe.
- `course_name` — jina la kozi; haliruhusu kurudiwa.
- `department` — idara ya kozi.
- `duration` — muda wa kozi.
- `created_at` — tarehe/saa ambayo rekodi iliingizwa.

Database inaitwa `adminlte_demo`. `config/database.php` itatengeneza table na sample courses mara ya kwanza unapofungua app. Hakuna haja ya ku-create table mwenyewe.

### Jinsi ya ku-run
1. Extract project folder `lesson` ndani ya `C:/xampp/htdocs/`.
2. Fungua XAMPP, washa **Apache** na **MySQL**.
3. Fungua `http://localhost/lesson/`.
4. Tumia sidebar kufungua **Analytics & Charts** na **Courses Table**.

**Kumbuka:** Project inatumia assets za AdminLTE/Bootstrap zilizopo ndani ya `adminlte/`, kwa hiyo components hizi hazihitaji internet baada ya folder kuhamishwa vizuri.
