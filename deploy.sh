session=github-actions-naru-aviation
tmux has-session -t $session || tmux new-session -d -s $session
tmux send-keys -t $session C-c
tmux send-keys -t $session "git reset --hard" C-m
tmux send-keys -t $session "git checkout main" C-m
tmux send-keys -t $session "git pull" C-m
tmux send-keys -t $session "composer install" C-m
tmux send-keys -t $session "npm install" C-m
tmux send-keys -t $session "npm run build" C-m
