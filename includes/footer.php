<style>
    .gh-footer {
        margin-top: 60px;
        background: linear-gradient(rgba(231, 136, 165, 0.92), rgba(192, 91, 121, 0.92)),
                 no-repeat center center/cover;
        border-top: 1px solid #ead8df;
        padding: 45px 30px 20px;
    }

    .gh-footer-container {
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 50px;
        flex-wrap: wrap;
    }

    .gh-footer-left,
    .gh-footer-right {
        flex: 1;
        min-width: 260px;
    }

    .gh-footer-logo {
        font-size: 46px;
        font-weight: bold;
        color: #2f3440;
        margin-bottom: 12px;
    }

    .gh-footer-desc {
        font-size: 18px;
        line-height: 1.6;
        color: #333;
        margin-bottom: 20px;
    }

    .gh-footer-socials {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .gh-footer-socials a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: #b07a46;
    color: white;
    border-radius: 50%;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    text-transform: uppercase;
    transition: 0.3s;
}

.gh-footer-socials {
    display: flex;
    gap: 12px;
}

.gh-footer-socials a {
    width: 42px;
    height: 42px;
    background: #c8b3a0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s;
}

.gh-footer-socials a img {
    width: 20px;
    height: 20px;
}

.gh-footer-socials a:hover {
    background: #debc9e;
    transform: scale(1.1);
}

.gh-footer-socials a:hover {
    background: #d5b79d;
    transform: scale(1.08);
}

.gh-footer-socials a:hover {
    background: #925f33;
    transform: scale(1.1);
}
    .gh-footer-right h3 {
        font-size: 24px;
        color: #222;
        margin-bottom: 14px;
    }

    .gh-footer-right p {
        font-size: 17px;
        color: #333;
        margin-bottom: 10px;
        line-height: 1.6;
    }

    .gh-footer-copy {
        max-width: 1100px;
        margin: 25px auto 0;
        padding-top: 15px;
        border-top: 1px solid rgba(0,0,0,0.08);
        text-align: center;
        font-size: 15px;
        color: #555;
    }

    @media (max-width: 768px) {
        .gh-footer {
            padding: 35px 20px 20px;
        }

        .gh-footer-logo {
            font-size: 36px;
        }

        .gh-footer-desc,
        .gh-footer-right p {
            font-size: 16px;
        }
    }
</style>

<footer class="gh-footer">
    <div class="gh-footer-container">
        <div class="gh-footer-left">
       <div class="gh-footer-logo">GiftHub</div>
            <p class="gh-footer-desc">
                Making every occasion special<br>
                with the perfect gift.
            </p>

        <div class="gh-footer-socials">
    <a href="#"><img src="assets/icons/facebook.png" alt="Facebook"></a>
    <a href="#"><img src="assets/icons/insta.jpg" alt="Instagram"></a>
    <a href="#"><img src="assets/icons/pintrest.png" alt="Pinterest"></a>
</div>
        </div>

        <div class="gh-footer-right">
            <h3>Contact Us</h3>
            <p>hello@gifthub.com</p>
            <p>+1 (559) 123-4567</p>
            <p>123 Gift Street, Celebration City</p>
        </div>
    </div>

    <div class="gh-footer-copy">
        © 2024 GiftHub. All rights reserved.
    </div>
</footer>

</body>
</html>