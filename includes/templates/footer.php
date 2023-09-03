<!-- <script src="./layout/fontawesome/all.min.js"></script> -->
</main>
<footer>
    <div class="wrapper flow">
        <div class="brand">
            <a href="index.php">
                <span class="observe">B</span>
                <span class="observe">e</span>
                <span class="observe">d</span>
                <span class="observe">d</span>
                <span class="observe">i</span>
                <span class="observe">.</span>
                <span class="observe">.</span>
            </a>
        </div>
        <div class="footer-category">
            <h2 class="footer-category-title">Categories</h2>
            <?php
            $stmt = $conn->prepare("SELECT id,cat_name FROM categories");
            $stmt->execute();
            $cats = $stmt->fetchAll();
            foreach ($cats as $cat) :
            ?>
                <div class="footer-category-box flow">
                    <a href="categories.php?id=<?php echo $cat["id"] ?>"><?php echo $cat["cat_name"] ?></a>
                    <div class="footer-category-box-links">
                        <?php
                        $stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE cat_id = ? AND acceptable = 1 AND available = 1");
                        $stmt->execute(array($cat["id"]));
                        $items = $stmt->fetchAll();
                        foreach ($items as $item) :
                        ?>
                            <a class="btn btn-primary" href="items.php?id=<?php echo $item["item_id"] ?>"><?php echo $item["item_name"] ?></a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div class="footer-contact">
            <h2>Contact & social</h2>
            <div class="links">
                <a class="btn btn-primary" href="tel:+963994241866"><i class="fa-solid fa-phone"></i> +963 (994) 241866</a>
                <a class="btn btn-primary" href="mailto:beddigroup@gmail.com"><i class="fa-solid fa-envelope"></i> beddigroup@gmail.com</a>
                <a class="btn btn-primary" href="#"><i class="fa-solid fa-location-dot"></i> Syria, Homs</a>
                <a class="btn btn-primary" href="https://www.facebook.com/BeddiShop" target="_blank">Facebook <i class="fa-brands fa-facebook"></i></i></a>
                <a class="btn btn-primary" href="https://www.instagram.com/beddishop/" target="_blank">Instagram <i class="fa-brands fa-instagram"></i></i></a>
            </div>
        </div>
        <p class="copy">Copyrights &copy; Beddi All Rights Reserved.</p>
        <div class="made-with">
            Made by <span>Abdullah Altatan</span>
            <a href="https://facebook.com/abdtt" target="_blank">
                <i class="fa-brands fa-facebook"></i>
            </a>
            <a href="https://instagram.com/abdullahalttan" target="_blank">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="https://github.com/aaltatan" target="_blank">
                <i class="fa-brands fa-github"></i>
            </a>
        </div>
</footer>

<script>
    const observedElementss = document.querySelectorAll(".observe");
    const elementsObservers = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("opened");
            } else {
                entry.target.classList.remove("opened");
            }
        });
    });
    observedElementss.forEach((observedElement) => {
        elementsObservers.observe(observedElement);
    });
</script>

<script src="./layout/js/layout.js"></script>

</body>

</html>