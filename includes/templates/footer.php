<!-- <script src="./layout/fontawesome/all.min.js"></script> -->
</main>
<footer>

    <section class="brand">
        <a href="index.php">
            <span class="observe">B</span>
            <span class="observe">e</span>
            <span class="observe">d</span>
            <span class="observe">d</span>
            <span class="observe">i</span>
            <span class="observe">.</span>
            <span class="observe">.</span>
        </a>
    </section>

    <section class="section-2">

        <div>
            <h2>Categories</h2>
            <div class="links">
                <?php
                $stmt = $conn->prepare("SELECT id,cat_name FROM categories LIMIT 5");
                $stmt->execute();
                $cats = $stmt->fetchAll();
                foreach ($cats as $cat) :
                ?>
                    <a href="categories.php?id=<?php echo $cat["id"] ?>"><?php echo $cat["cat_name"] ?></a>
                <?php endforeach ?>
                <a href="categories.php">See More</a>
            </div>
        </div>

        <div>
            <h2>Specials</h2>
            <div class="links">
                <?php
                $stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE acceptable = 1 AND available = 1 AND is_special = 1 LIMIT 5");
                $stmt->execute();
                $items = $stmt->fetchAll();
                foreach ($items as $item) :
                ?>
                    <a href="items.php?id=<?php echo $item["item_id"] . '&itemid=' . strtolower(str_replace(" ", "_", $item["item_name"])) ?>"><?php echo $item["item_name"] ?></a>
                <?php endforeach ?>
                <a href="items.php">See More</a>
            </div>
        </div>

        <div>
            <h2>About</h2>
            <div class="links">
                <a href="tel:+963994241866"><i class="fa-solid fa-phone"></i> +963 (994) 241866</a>
                <a href="mailto:beddigroup@gmail.com"><i class="fa-solid fa-envelope"></i> beddigroup@gmail.com</a>
                <a href="#"><i class="fa-solid fa-location-dot"></i> Syria, Homs</a>
                <a href="https://www.facebook.com/BeddiShop" target="_blank"><i class="fa-brands fa-facebook"></i> Facebook</a>
                <a href="https://www.instagram.com/beddishop/" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram</a>
            </div>
        </div>

        <div>
            <h2>Gallery</h2>
            <div class="images">
                <?php
                $stmt = $conn->prepare("SELECT 
                                                items_images.img
                                            FROM 
                                                items_images
                                            LEFT JOIN
                                                items
                                            ON
                                                items.item_id = items_images.item_id
                                            WHERE
                                                items.is_cover = 1
                                            LIMIT 6
                                            ");
                $stmt->execute();
                $images = $stmt->fetchAll();
                foreach ($images as $image) :
                ?>
                    <img src="<?php echo substr($image["img"], 1) ?>" alt="dasd">
                <?php endforeach ?>
            </div>
        </div>

    </section>

    <section class="copyright">
        <p>Copyrights &copy; Beddi All Rights Reserved.</p>
        <div>
            Made with 💙 by <span>Abdullah Altatan</span>
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
    </section>

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