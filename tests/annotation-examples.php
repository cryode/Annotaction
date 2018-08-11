<?php
declare(strict_types=1);

/**
 * A collection of different valid @Route annotation definitions
 * to test compatibility with.
 *
 * Single path:
 * @Route("/")
 * @Route("static")
 * @Route("dynamic/{id}")
 *
 * Explicit path key:
 * @Route(path="")
 * @Route(path="static")
 * @Route(path="dynamic/{slug}")
 *
 * HTTP Methods:
 * @Route("/", method="GET")
 * @Route("/", method="HEAD")
 * @Route("/", method="POST")
 * @Route("/", method="PUT")
 * @Route("/", method="PATCH")
 * @Route("/", method="DELETE")
 * @Route("/", method="OPTIONS")
 * @Route("/", method="ANY")
 * @Route("/", method={"GET", "POST"})
 * @Route("/", method={"GET", "GET") - Remove duplicates
 * @Route("/", method="get, post, PUT") - Comma-delimited lists accepted also
 * @Route("/", method="get") - Case insensitive
 *
 * Pattern matching:
 * @Route("/post/{id}", where={"id": "\d+"})
 *
 * All the things!:
 * @Route("/user/{user}"
 *        method="PUT"
 *        where={"user": "\d+"}
 *        middleware="web"
 *        name="user.update")
 *
 * === BAD EXAMPLES! Ensure these fail. ===
 *
 * Invalid HTTP Method:
 * @Route("/", method="FOO")
 * @Route("/", method={"GET", "FOO")
 *
 * No key => value format for where:
 * @Route("/user/{user}", where="\d+")
 * @Route("/user/{user}", where={"\d+"})
 */
