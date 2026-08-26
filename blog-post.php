<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Get post ID from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch blog post details
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND is_published = 1");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: blog.php');
    exit;
}

// Increment view count
$stmt = $pdo->prepare("UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$postId]);

// Fetch related posts
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE category = ? AND id != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 4");
$stmt->execute([$post['category'], $postId]);
$relatedPosts = $stmt->fetchAll();
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><?php echo htmlspecialchars($post['category']); ?></p>
      </section>

      <section class="ma-blog-post">
        <div class="ma-blog-post-inner">
          <div class="ma-blog-post-main">
            <div class="ma-blog-post-header">
              <div class="ma-blog-post-meta">
                <span class="ma-blog-post-category"><?php echo htmlspecialchars($post['category']); ?></span>
                <span class="ma-blog-post-date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                <span class="ma-blog-post-author">By <?php echo htmlspecialchars($post['author']); ?></span>
              </div>
              <?php if ($post['image_url']): ?>
                <div class="ma-blog-post-featured">
                  <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
              <?php endif; ?>
            </div>

            <div class="ma-blog-post-content">
              <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <div class="ma-blog-post-footer">
              <div class="ma-blog-post-share">
                <h4>Share this article</h4>
                <div class="ma-share-buttons">
                  <a href="#" class="ma-share-btn" aria-label="Share on Facebook">f</a>
                  <a href="#" class="ma-share-btn" aria-label="Share on Twitter">𝕏</a>
                  <a href="#" class="ma-share-btn" aria-label="Share on WhatsApp">📱</a>
                  <a href="#" class="ma-share-btn" aria-label="Copy link">🔗</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ma-blog-post-sidebar">
            <div class="ma-sidebar-card">
              <h3>About the Author</h3>
              <div class="ma-author-info">
                <div class="ma-author-avatar">
                  <span><?php echo strtoupper(substr(htmlspecialchars($post['author']), 0, 2)); ?></span>
                </div>
                <div class="ma-author-details">
                  <h4><?php echo htmlspecialchars($post['author']); ?></h4>
                  <p>Islamic Scholar & Educator</p>
                </div>
              </div>
            </div>

            <?php if (!empty($relatedPosts)): ?>
              <div class="ma-sidebar-card">
                <h3>Related Articles</h3>
                <div class="ma-related-posts">
                  <?php foreach ($relatedPosts as $related): ?>
                    <a href="blog-post.php?id=<?php echo $related['id']; ?>" class="ma-related-post">
                      <div class="ma-related-post-img" style="background: linear-gradient(135deg, #1a4a2a 0%, #2d6a4a 100%);">
                        <span>📖</span>
                      </div>
                      <div class="ma-related-post-info">
                        <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                        <span><?php echo date('M j, Y', strtotime($related['created_at'])); ?></span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="ma-sidebar-card">
              <h3>Categories</h3>
              <div class="ma-categories-list">
                <a href="blog.php" class="ma-category-tag">Quran</a>
                <a href="blog.php" class="ma-category-tag">Arabic</a>
                <a href="blog.php" class="ma-category-tag">Islamic Studies</a>
                <a href="blog.php" class="ma-category-tag">Hadith</a>
                <a href="blog.php" class="ma-category-tag">Seerah</a>
                <a href="blog.php" class="ma-category-tag">Fiqh</a>
              </div>
            </div>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
