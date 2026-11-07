<!DOCTYPE html>
<html>
    <head>
        <title>奧羅拉 | 相片集</title>
    	<link rel="icon" type="image/x-icon" href="img/others/Birthmark.png">
        <style>
            body {
                margin: 0;
                padding: 0 0 70px 0;
                color: white;
                background: black;
                font-family: sans-serif;
                scroll-behavior: smooth;
            }
            a { color: #00d4ff; text-decoration: none; }
            
            /* 快速導航欄 */
            .nav-bar {
                position: sticky;
                top: 0;
                background: rgba(20, 20, 20, 0.95);
                padding: 15px;
                z-index: 1000;
                text-align: center;
                border-bottom: 1px solid #444;
            }
            .nav-bar a {
                margin: 0 12px;
                font-size: 16px;
                font-weight: bold;
                display: inline-block;
                transition: 0.2s;
            }
            .nav-bar a:hover { color: white; }

            /* 摺疊組件 */
            details {
                margin: 15px auto;
                width: 95%;
                border: 1px solid #333;
                border-radius: 8px;
            }
            summary {
                font-size: 1.3em;
                padding: 15px;
                background: #1a1a1a;
                color: #fff;
                cursor: pointer;
                list-style: none;
            }
            summary:hover { background: #252525; }
            summary::before { content: "📁 "; margin-right: 10px; }
            details[open] summary::before { content: "📂 "; }

            .gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
                padding: 20px;
                background: #0a0a0a;
            }

            /* 白色外框樣式 */
            .item-link {
                text-decoration: none;
                display: block;
                transition: transform 0.2s;
            }
            .item-link:hover { transform: scale(1.03); }

            .img-container { 
                width: 220px; text-align: center; border: 1px solid #333; 
                padding: 5px; border-radius: 8px; background: #fff; 
            }
            .img-container img { width: 100%; height: 160px; object-fit: cover; border-radius: 4px; }
            
            /* 音頻容器樣式 */
            .audio-container {
                width: 320px; padding: 15px; background: #fff; border-radius: 8px; text-align: center;
                border: 1px solid #333;
            }
            .audio-container audio { width: 100%; margin-top: 10px; }
            .twine-link-box {
                width: 90%; font-size: 10px; margin-top: 8px; 
                padding: 5px; border: 1px solid #ccc; border-radius: 4px;
                background: #eee; color: #333; cursor: pointer;
            }

            .file-name { color: #333; font-size: 11px; margin-top: 8px; word-break: break-all; }
        </style>
    </head>
    <body>
        <h1 style="text-align:center; margin: 30px 0;">相片集</h1>

        <?php
        $sources = ['img', 'audio'];
        $allFiles = [];

        // 掃描檔案
        foreach ($sources as $root) {
            if (is_dir($root)) {
                $directory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
                foreach ($directory as $file) {
                    if ($file->isFile() && preg_match('/\.(jpg|jpeg|png|gif|mp3|wav|ogg)$/i', $file->getFilename())) {
                        $allFiles[] = $file->getPathname();
                    }
                }
            }
        }
        sort($allFiles);

        if (!empty($allFiles)) {
            // --- 步驟 1: 第一層目錄導覽 ---
            $topFolders = [];
            foreach ($allFiles as $f) {
                $parts = explode(DIRECTORY_SEPARATOR, $f);
                if (count($parts) > 1) { $topFolders[] = $parts[0]; }
            }
            $topFolders = array_unique($topFolders);

            echo '<div class="nav-bar"><strong>快速跳轉：</strong>';
            foreach ($topFolders as $tf) {
                $navId = "nav-" . md5($tf);
                echo "<a href='#$navId'>$tf</a>";
            }
            echo '</div>';

            // --- 步驟 2: 生成內容 ---
            $currentFolder = '';
            $isFirst = true;

            foreach ($allFiles as $fullPath) {
                $folderPath = dirname($fullPath);

                if ($currentFolder !== $folderPath) {
                    if (!$isFirst) echo '</div></details>';
                    $isFirst = false;
                    $currentFolder = $folderPath;
                    
                    // 根據第一層目錄生成錨點 ID
                    $parts = explode(DIRECTORY_SEPARATOR, $fullPath);
                    $anchorId = "nav-" . md5($parts[0]);

                    echo "<details id='$anchorId'>";
                    echo "<summary>$currentFolder</summary>";
                    echo '<div class="gallery">';
                }

                $fileName = basename($fullPath);
                $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                
                // 處理連結編碼，確保 Twine 可以讀取帶有空格的檔名
                $encodedPath = str_replace("%2F", "/", rawurlencode($fullPath));
                $fullURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/" . $encodedPath;

                if (in_array($ext, ['mp3', 'wav', 'ogg'])) {
                    // 音頻模塊
                    echo '<div class="audio-container">';
                    echo '  <div class="file-name">' . $fileName . '</div>';
                    echo '  <audio controls><source src="' . $encodedPath . '" type="audio/' . ($ext == 'mp3' ? 'mpeg' : $ext) . '"></audio>';
                    echo '</div>';
                } else {
                    // 圖片模塊
                    echo '<a href="' . $encodedPath . '" target="_blank" class="item-link">';
                    echo '  <div class="img-container">';
                    echo '    <img src="' . $encodedPath . '" alt="Image" loading="lazy">';
                    echo '    <div class="file-name">' . $fileName . '</div>';
                    echo '  </div>';
                    echo '</a>';
                }
            }
            echo '</div></details>'; 
        } else {
            echo "<p style='text-align:center;'>No media files found.</p>";
        }
        ?>

		<div style="text-align:center; margin-top: 50px;">
		    <a href="indexCHT.html" style="font-size: 20px; color: white;">← 返回</a>
		</div>
    </body>
</html>
