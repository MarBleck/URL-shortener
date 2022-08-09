<footer class="pagefooter">
                    <div class="footerd">
                        <div class="fd footerdiv">
                            <p class="right">&copy; 2022 Marlon Bleckwehl</p>
                            <p class="right"><a href="https://github.com/marlonbde/URL-shortener/blob/main/license" target="_blank" rel="noopener noreferrer">This program is licensed under the BSD 2-Clause "Simplified" License</a></p>
                        </div>
                        <div class="fd footerdiv">
                            <nav class="mainnav">
                                <ul class="navul">
                                    <li class="navli"><a href="/admin/index.php" class="nava nava1">Shortener</a></li>
                                    <li class="navli"><a href="/admin/settings.php" class="nava nava1">Settings</a></li>
                                    <li class="navli"><a href="/admin/logout.php" class="nava nava1">Logout</a></li>
                                    <?php if ($show_github_link == true) {
                                        echo '<li class="navli"><a href="https://github.com/marlonbde/URL-shortener/" target="_blank" class="nava nava1"><i class="bi bi-github"></i></a></li>';
                                    } else {} ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </footer>