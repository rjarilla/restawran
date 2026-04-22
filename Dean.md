# Dean Notes

Added this one so I can run the dev script on Windows without WSL. It doesn’t work with the pail command, but it will at least run the server, queue, and vite commands. CHATGPT’ed the solution, so if you have a better one, please let me know! I also added the colors to make it easier to see which command is which in the terminal.
```
"dev:win": [
            "Composer\\Config::disableProcessTimeout",
            "npx concurrently -c \"#93c5fd,#c4b5fd,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"npm run dev\" --names=server,queue,vite --kill-others"
        ],
```