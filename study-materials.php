<?php 
require_once 'config.php';

// Get database connection
$pdo = getDB();

// Fetch all study materials with course name
try {
    $stmt = $pdo->query("
        SELECT sm.*, c.title as course_title 
        FROM study_materials sm 
        LEFT JOIN courses c ON sm.course_id = c.id 
        ORDER BY sm.is_featured DESC, sm.download_count DESC, sm.created_at DESC
    ");
    $materials = $stmt->fetchAll();
} catch (PDOException $e) {
    $materials = [];
}

// Group materials by category
$groupedMaterials = [];
foreach ($materials as $material) {
    $category = $material['category'] ?: 'General';
    if (!isset($groupedMaterials[$category])) {
        $groupedMaterials[$category] = [];
    }
    $groupedMaterials[$category][] = $material;
}

// Get featured materials
$featuredMaterials = array_filter($materials, function($m) {
    return $m['is_featured'] == 1;
});

include 'header.php'; 
?>
      <section class="ma-page-banner">
        <h1>Study Materials</h1>
        <p>Free downloadable resources for every course — PDFs, worksheets, and references</p>
      </section>

      <section style="background: #f7f7f4; padding: 28px 24px; border-bottom: 1px solid #eee;">
        <div style="max-width: 900px; margin: 0 auto; display: flex; justify-content: center; gap: 48px; flex-wrap: wrap;">
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">80+</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Resources</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">200K+</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Total Downloads</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">4</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Categories</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">Free</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Always</div>
          </div>
        </div>
      </section>

      <section style="padding: 50px 24px 0; max-width: 1100px; margin: 0 auto;">
        <h2 style="font-weight: 800; font-size: 22px; margin-bottom: 20px;">⭐ Featured Resources</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 50px;">
          <?php if (empty($featuredMaterials)): ?>
            <p style="grid-column: 1/-1; text-align: center; color: #888; padding: 20px;">No featured materials yet.</p>
          <?php else: ?>
            <?php foreach (array_slice($featuredMaterials, 0, 3) as $material): ?>
              <div style="background: #0b2b2b; border-radius: 12px; padding: 24px; position: relative; overflow: hidden;">
                <span style="position: absolute; top: 12px; right: 12px; background: #c9a227; color: #0b2b2b; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px;">Featured</span>
                <div style="font-size: 36px; margin-bottom: 12px;">�</div>
                <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 8px;"><?php echo htmlspecialchars($material['title']); ?></h3>
                <p style="font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.6; margin-bottom: 16px;"><?php echo htmlspecialchars($material['description'] ?: 'Download this study resource for your learning journey.'); ?></p>
                <?php if ($material['course_title']): ?>
                  <p style="font-size: 12px; color: rgba(255,255,255,0.5); margin-bottom: 12px;">Course: <?php echo htmlspecialchars($material['course_title']); ?></p>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($material['file_url']); ?>" class="ma-download-btn" download onclick="trackDownload(<?php echo $material['id']; ?>)">
                  <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  Download Free
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <section style="padding: 0 24px 60px; max-width: 1100px; margin: 0 auto;">
        <?php if (empty($groupedMaterials)): ?>
          <p style="text-align: center; color: #888; padding: 40px;">No study materials available yet.</p>
        <?php else: ?>
          <?php foreach ($groupedMaterials as $category => $categoryMaterials): ?>
            <div style="margin-bottom: 40px;">
              <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 4px; height: 24px; background: #c9a227; border-radius: 2px;"></div>
                <h2 style="font-size: 20px; font-weight: 800; color: #111;"><?php echo htmlspecialchars(ucfirst($category)); ?></h2>
              </div>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                <?php foreach ($categoryMaterials as $material): ?>
                  <div class="ma-resource-card">
                    <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                      <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #0d3b2e, #1a5c3e); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c9a227" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                      </div>
                      <div style="min-width: 0;">
                        <div style="font-size: 13.5px; font-weight: 600; color: #111; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($material['title']); ?></div>
                        <div style="font-size: 11.5px; color: #999; margin-top: 2px;">
                          <?php echo strtoupper(htmlspecialchars($material['file_type'] ?: 'FILE')); ?> · <?php echo htmlspecialchars($material['file_size'] ?: '-'); ?> · <?php echo number_format($material['download_count']); ?> downloads
                        </div>
                      </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($material['file_url']); ?>" class="ma-icon-dl-btn" download onclick="trackDownload(<?php echo $material['id']; ?>)" aria-label="Download <?php echo htmlspecialchars($material['title']); ?>">
                      <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
<?php include 'footer.php'; ?>

<script>
function trackDownload(materialId) {
  // Send AJAX request to track download
  fetch('track-download.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'material_id=' + materialId
  }).catch(error => console.error('Error tracking download:', error));
}
</script>
