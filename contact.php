<?php
require_once 'config.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Verify CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email address.';
    } else {
        try {
            $db = getDB();
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$name, $email, $subject, $message, $ipAddress]);
            
            if ($result) {
                $success = 'Your message has been sent successfully! We will get back to you soon.';
            } else {
                $error = 'Failed to send message. Please try again.';
            }
        } catch (PDOException $e) {
            logError('Database error during contact form submission', ['error' => $e->getMessage()]);
            $error = 'Failed to send message. Please try again.';
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1>Contact Us</h1>
        <p>Get in touch with our team for any questions or support</p>
      </section>

      <section class="ma-contact-section">
        <div class="ma-contact-wrapper">
          <div class="ma-contact-info">
            <h2>Get in Touch</h2>
            <p>We're here to help you with any questions about our courses, enrollment, or general inquiries.</p>
            
            <div class="ma-contact-details">
              <div class="ma-contact-item">
                <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a227" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <div>
                  <strong>Address</strong>
                  <span>Baliapur, Dhanbad, Jharkhand, 828201</span>
                </div>
              </div>
              <div class="ma-contact-item">
                <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a227" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <div>
                  <strong>Email</strong>
                  <span>khanumar865446@gmail.com</span>
                </div>
              </div>
              <div class="ma-contact-item">
                <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#c9a227" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.25h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 7.09 7.09l.91-1.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <div>
                  <strong>Phone</strong>
                  <span>+91 8409281635</span>
                </div>
              </div>
            </div>

            <div class="ma-contact-social">
              <h4>Follow Us</h4>
              <div class="ma-social-icons">
                <a href="#" class="ma-social-icon" aria-label="Facebook">f</a>
                <a href="#" class="ma-social-icon" aria-label="YouTube">▶</a>
                <a href="#" class="ma-social-icon" aria-label="Instagram">📷</a>
                <a href="#" class="ma-social-icon" aria-label="Telegram">✈</a>
              </div>
            </div>
          </div>

          <div class="ma-contact-form-wrapper">
            <h2>Send us a Message</h2>
            
            <?php if ($error): ?>
            <div style="background: #fee; border: 1px solid #fcc; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #c33; font-size: 14px;">
              <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div style="background: #efe; border: 1px solid #cfc; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #3c3; font-size: 14px;">
              <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <form class="ma-contact-form" method="POST" action="">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
              
              <div class="ma-form-group">
                <label for="contact-name">Full Name</label>
                <input type="text" id="contact-name" name="name" placeholder="Your name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" />
              </div>
              <div class="ma-form-group">
                <label for="contact-email">Email Address</label>
                <input type="email" id="contact-email" name="email" placeholder="you@email.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
              </div>
              <div class="ma-form-group">
                <label for="contact-subject">Subject</label>
                <input type="text" id="contact-subject" name="subject" placeholder="How can we help?" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" />
              </div>
              <div class="ma-form-group">
                <label for="contact-message">Message</label>
                <textarea id="contact-message" name="message" rows="5" placeholder="Your message..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
              </div>
              <button type="submit" class="ma-btn-gold">Send Message</button>
            </form>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
