<?php
require 'pdo.php';

// Check if viewing a bundle
$slug = $_GET['s'] ?? null;
if ($slug) {
    $stmt = $pdo->prepare("SELECT b.bundle_name, l.link name_title, l.destination_url 
                           FROM bundles b 
                           JOIN bundle_links l ON b.id = l.bundle_id
                           WHERE b.slug = ?");
    $stmt->execute([$slug]);
    $bundle_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$bundle_data) { $error = "Bundle not found."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
  body {
    background: radial-gradient(circle at top, #0f172a, #020617);
    color: #e2e8f0;
    font-family: 'Inter', sans-serif;
  }

  /* Glass card effect */
  .glass {
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(18px);
    border: 1px solid rgba(96, 165, 250, 0.25);
    border-radius: 20px;
    transition: 0.3s ease;
  }

  .glass:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 40px rgba(37, 99, 235, 0.25);
  }

  /* Glow effect */
  .blue-glow {
    box-shadow: 0 0 25px rgba(59, 130, 246, 0.4);
  }

  /* Button style */
  .accent-blue {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border-radius: 10px;
    font-weight: 600;
    transition: 0.25s;
  }

  .accent-blue:hover {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    transform: scale(1.05);
  }

  /* Input style */
  .input-box {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(96, 165, 250, 0.3);
    border-radius: 10px;
    color: white;
    padding: 8px 12px;
    transition: 0.2s;
  }

  .input-box:focus {
    border-color: #60a5fa;
    outline: none;
    box-shadow: 0 0 10px rgba(96, 165, 250, 0.5);
  }
</style>

</head>
<body class="min-h-screen p-4 md:p-10 flex flex-col items-center">

 <?php if ($slug): ?>
    <!-- VIEWING A BUNDLE -->
    <div class="max-w-md w-full text-center mt-10">

        <?php if (isset($error)): ?>
            <h1 class="text-2xl font-bold text-red-400"><?= htmlspecialchars($error) ?></h1>
            <a href="index.php" class="text-blue-400 underline mt-4 block">Go Back</a>

        <?php else: ?>
            <div class="mb-8">
                <div class="w-16 h-16 accent-blue rounded-2xl mx-auto mb-4 flex items-center justify-center rotate-3 blue-glow">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                        </path>
                    </svg>
                </div>

                <h1 class="text-3xl font-black uppercase tracking-tighter">
                    <?= htmlspecialchars($bundle_data[0]['bundle_name']) ?>
                </h1>
                <p class="text-slate-400 text-sm">Created via LinkBolt</p>
            </div>

            <div class="space-y-4">
                <?php foreach ($bundle_data as $link): ?>
                    <a href="<?= htmlspecialchars($link['destination_url']) ?>" target="_blank"
                       class="block p-5 glass rounded-2xl font-bold hover:scale-[1.02] transition-transform border-l-4 border-l-blue-500">
                        <?= htmlspecialchars($link['link_title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
<?php endif; ?>

        </div>

    <?php else: ?>
        <!-- DASHBOARD -->
        <div class="max-w-2xl w-full">
            <header class="flex justify-between items-center mb-12">
                <div class="text-3xl font-black italic tracking-tighter text-blue-500">LINK<span class="text-white">BOLT</span></div>
                <div id="user-tag" class="text-[10px] uppercase tracking-widest bg-slate-800 px-3 py-1 rounded text-slate-400"></div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Creator Panel -->
                <div class="glass p-6 rounded-3xl blue-glow">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <span class="w-2 h-6 accent-blue rounded-full"></span> New Bundle
                    </h2>
                    <div class="space-y-4">
                        <input type="text" id="b-name" placeholder="Bundle Name (e.g. My Socials)" class="w-full p-3 rounded-xl input-box">
                        <input type="text" id="b-slug" placeholder="custom-slug" class="w-full p-3 rounded-xl input-box">
                        
                        <div class="pt-4 border-t border-slate-700">
                            <p class="text-xs text-slate-500 mb-2 uppercase font-bold">Add Links to this bundle</p>
                            <div id="links-builder" class="space-y-2 mb-4">
                                <div class="flex gap-2">
                                    <input type="text" placeholder="Title" class="w-1/3 p-2 text-sm rounded-lg input-box link-title-in">
                                    <input type="url" placeholder="URL" class="w-2/3 p-2 text-sm rounded-lg input-box link-url-in">
                                </div>
                            </div>
                            <button onclick="addLinkField()" class="text-xs text-blue-400 hover:text-blue-300">+ Add Another URL</button>
                        </div>

                        <button onclick="createBundle()" class="w-full accent-blue p-4 rounded-xl font-bold text-white mt-4 shadow-lg active:scale-95 transition-all">
                            CREATE BOLT LINK
                        </button>
                    </div>
                </div>

                <!-- History Panel -->
                <div>
                    <h2 class="text-xl font-bold mb-4 text-slate-400">Your Active Bolts</h2>
                    <div id="my-bundles" class="space-y-4">
                        <!-- Loaded via JS -->
                    </div>
                </div>
            </div>
        </div>

        <script>
          // Generate or get user ID
let userId = localStorage.getItem('lb_uid');

if (!userId) {
    userId = `u_${crypto.randomUUID().slice(0, 8)}`;
    localStorage.setItem('lb_uid', userId);
}

// Show user ID on screen
const userTag = document.getElementById('user-tag');
if (userTag) userTag.textContent = `ID: ${userId}`;


// Add new link input row
function addLinkField() {
    const wrapper = document.createElement('div');
    wrapper.classList.add('flex', 'gap-2');

    const titleInput = document.createElement('input');
    titleInput.type = 'text';
    titleInput.placeh

          async function createBundle() {
    try {
        const name = document.getElementById('b-name').value.trim();
        const slug = document.getElementById('b-slug').value.trim();

        if (!name || !slug) {
            alert("Please enter bundle name and slug.");
            return;
        }

        const titles = [...document.querySelectorAll('.link-title-in')].map(i => i.value.trim());
        const urls   = [...document.querySelectorAll('.link-url-in')].map(i => i.value.trim());

        const links = titles
            .map((t, i) => ({ title: t, url: urls[i] }))
            .filter(l => l.title && l.url);

        if (links.length === 0) {
            alert("Add at least one valid link.");
            return;
        }

        const res = await fetch('api.php?action=create', {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id: userId, name, slug, links })
        });

        const out = await res.json();

        if (out.success) {
            alert("✅ Bundle created successfully!");
            location.reload();
        } else {
            alert(out.error || "Slug already taken or error occurred.");
        }

    } catch (err) {
        console.error(err);
        alert("Something went wrong. Try again.");
    }
}


async function loadBundles() {
    try {
        const res = await fetch(`api.php?action=list&user_id=${userId}`);
        const bundles = await res.json();
        const container = document.getElementById('my-bundles');

        container.innerHTML = "";

        if (bundles.length === 0) {
            container.innerHTML = `<p class="text-slate-500 text-sm">No bundles created yet.</p>`;
            return;
        }

        bundles.forEach(b => {
            const url = `${window.location.origin}${window.location.pathname}?s=${b.slug}`;

            container.innerHTML += `
                <div class="p-4 glass rounded-2xl border-l-2 border-blue-500 hover:scale-[1.02] transition">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-bold text-lg">${b.bundle_name}</span>
                        <span class="text-[10px] bg-blue-500/20 text-blue-400 px-2 py-1 rounded uppercase">
                            ${b.link_count} Links
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="text" value="${url}" readonly
                            class="w-full bg-black/30 p-2 text-xs rounded border border-white/10 text-slate-400">

                        <button onclick="navigator.clipboard.writeText('${url}')"
                            class="text-xs bg-blue-500 text-white px-3 py-2 rounded font-bold hover:bg-blue-600">
                            Copy
                        </button>

                        <button onclick="window.open('${url}')"
                            class="text-xs bg-white text-black px-3 py-2 rounded font-bold">
                            Open
                        </button name>
                    </div>
                </div>
            `;
        });

    } catch (err) {
        console.error(err);
        document.getElementById('my-bundles').innerHTML =
            `<p class="text-red-400 text-sm">Failed to load bundles.</p>`;
    }
}

loadBundles();
              const res = await fetch(`api.php?action=list&user_id=${userId}`);
const bundles = await res.json();
const container = document.getElementById('my-bundles');

container.innerHTML = "";

if (!bundles.length) {
    container.innerHTML = `<p class="text-slate-500 text-sm">No bundles yet. Create your first one 🚀</p>`;
    return;
}

bundles.forEach(bundle => {
    const url = `${window.location.origin}${window.location.pathname}?s=${bundle.slug}`;

  async function loadBundles() {
    const container = document.getElementById("my-bundles");

    try {
        const response = await fetch(`api.php?action=list&user_id=${userId}`);
        const data = await response.json();

        // clear container safely
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }

        // empty state
        if (!Array.isArray(data) || data.length === 0) {
            const msg = document.createElement("p");
            msg.className = "text-slate-500 text-sm";
            msg.textContent = "No bundles available. Start by creating one 🚀";
            container.appendChild(msg);
            return;
        }

        // render bundles
        for (const item of data) {
            const linkURL = `${location.origin}${location.pathname}?s=${item.slug}`;

            const card = document.createElement("div");
            card.className =
                "p-4 glass rounded-2xl border-l-2 border-blue-500 transition " +
                "hover:shadow-lg hover:-translate-y-0.5";

            const topRow = `
                <div class="flex justify-between mb-2">
                    <strong class="text-lg">${item.bundle_name}</strong>
                    <span class="text-[10px] bg-blue-500/20 text-blue-400 px-2 py-1 rounded">
                        ${item.link_count} Links
                    </span>
                </div>
            `;

            const bottomRow = `
                <div class="flex gap-2">
                    <input value="${linkURL}" readonly
                        class="flex-1 bg-black/30 p-2 text-xs rounded border border-white/10 text-slate-400">

                    <button class="copy text-xs bg-blue-500 text-white px-3 rounded">
                        Copy
                    </button>

                    <button class="open text-xs bg-white text-black px-3 rounded">
                        Open
                    </button>
                </div>
            `;

            card.innerHTML = topRow + bottomRow;

            // actions
            card.querySelector(".copy").onclick = () => navigator.clipboard.writeText(linkURL);
            card.querySelector(".open").onclick = () => window.open(linkURL, "_blank", "noopener");

            container.appendChild(card);
        }

    } catch (error) {
        container.innerHTML =
            `<p class="text-red-400 text-sm">Unable to load bundles right now.</p>`;
        console.error("Bundle load error:", error);
    }
}

// call function
loadBundles();

    `;

    // Copy button action
    card.querySelector('.copy-btn').onclick = () => {
        navigator.clipboard.writeText(url);
    };

    // Open button action
    card.querySelector('.open-btn').onclick = () => {
        window.open(url, "_blank");
    };

    container.appendChild(card);
});

                            </div>
                        </div>
                    `;
                });
            }
            loadBundles();
        </script>
    <?php endif; ?>
</body>
</html>
