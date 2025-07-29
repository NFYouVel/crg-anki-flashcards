<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tree View</title>
    <style>
        body {
            background: #1e1e1e;
            color: white;
            font-family: sans-serif;
        }

        ul.tree {
            list-style: none;
            padding-left: 20px;
        }

        li {
            position: relative;
        }

        .tree-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2px 10px;
            cursor: default;
        }

        .folder::before {
            content: "📁 ";
        }

        .deck::before {
            content: "🃏 ";
        }

        .toggle {
            cursor: pointer;
            margin-right: 5px;
            width: 16px;
            display: inline-block;
            text-align: center;
        }

        .collapsed > ul {
            display: none;
        }

        input[type="checkbox"] {
            margin-left: auto;
        }
    </style>
</head>
<body>

<ul class="tree">
    <li>
        <div class="tree-item">
            <span class="toggle">+</span>
            <span class="folder">Master Deck Folder</span>
            <input type="checkbox">
        </div>
        <ul>
            <li>
                <div class="tree-item">
                    <span class="toggle">+</span>
                    <span class="folder">Active Chinese</span>
                    <input type="checkbox">
                </div>
                <ul>
                    <li>
                        <div class="tree-item">
                            <span class="folder">HSK</span>
                            <input type="checkbox" checked>
                        </div>
                    </li>
                    <li>
                        <div class="tree-item">
                            <span class="toggle">-</span>
                            <span class="folder">TOCFL</span>
                            <input type="checkbox">
                        </div>
                        <ul>
                            <li><div class="tree-item"><span class="folder">TOCFL A1</span><input type="checkbox"></div></li>
                            <li><div class="tree-item"><span class="folder">TOCFL A2</span><input type="checkbox"></div></li>
                            <li>
                                <div class="tree-item">
                                    <span class="toggle">-</span>
                                    <span class="folder">TOCFL B1</span>
                                    <input type="checkbox" checked>
                                </div>
                                <ul>
                                    <li><div class="tree-item"><span class="deck">Deck 01</span><input type="checkbox" checked></div></li>
                                    <li><div class="tree-item"><span class="deck">Deck 02</span><input type="checkbox" checked></div></li>
                                    <li><div class="tree-item"><span class="deck">Deck 03</span><input type="checkbox" checked></div></li>
                                </ul>
                            </li>
                            <li><div class="tree-item"><span class="folder">TOCFL B2</span><input type="checkbox"></div></li>
                            <li>
                                <div class="tree-item">
                                    <span class="toggle">-</span>
                                    <span class="folder">TOCFL C1</span>
                                    <input type="checkbox">
                                </div>
                                <ul>
                                    <li><div class="tree-item"><span class="deck">Deck 01</span><input type="checkbox"></div></li>
                                    <li><div class="tree-item"><span class="deck">Deck 02</span><input type="checkbox"></div></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

<script>
    // Expand/Collapse logic
    document.querySelectorAll('.toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const li = toggle.closest("li");
            li.classList.toggle("collapsed");
            toggle.textContent = li.classList.contains("collapsed") ? "+" : "-";
        });
    });
</script>

</body>
</html>
