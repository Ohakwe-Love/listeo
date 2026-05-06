<?php include_once "assets/extras/header.php"; ?>

<section class="page-hero contact-hero">
    <div class="page-inner">
        <span class="page-kicker">Contact</span>
        <h1>Need help, partnership details, or listing support?</h1>
        <p>Reach out to Listeo for business listings, customer support, partnerships, and product questions.</p>
        <div class="hero-stats">
            <span><strong>Fast</strong> response</span>
            <span><strong>Vendor</strong> support</span>
            <span><strong>Local</strong> enquiries</span>
        </div>
    </div>
</section>

<section class="page-section page-muted-band">
    <div class="page-inner contact-layout">
        <aside class="contact-panel">
            <span class="page-kicker">Get in touch</span>
            <h2>We are ready to help.</h2>
            <p class="contact-lead">Use this page for partnership enquiries, listing requests, and customer support.</p>
            <p><span class="contact-detail">Email</span><a href="mailto:ohakwemuna@gmail.com">ohakwemuna@gmail.com</a></p>
            <p><span class="contact-detail">Phone</span>+2348161452508</p>
            <p><span class="contact-detail">Office</span>12345 Wood Creeks Road</p>
            <div class="contact-social-row">
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </aside>

        <form class="contact-form" action="#" method="post">
            <div class="form-heading">
                <span class="page-kicker">Send message</span>
                <h2>Tell us what you need.</h2>
            </div>
            <input type="text" name="name" placeholder="Your name" required>
            <input type="email" name="email" placeholder="Email address" required>
            <select name="subject" required>
                <option value="">What can we help with?</option>
                <option value="listing">List my business</option>
                <option value="support">Customer support</option>
                <option value="partnership">Partnership</option>
            </select>
            <textarea name="message" placeholder="Tell us a little more" required></textarea>
            <button class="page-btn page-btn-primary" type="submit">send message</button>
        </form>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
