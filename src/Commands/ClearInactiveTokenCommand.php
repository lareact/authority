<?php


namespace Golly\Authority\Commands;


use Golly\Authority\Models\UserAccessToken;
use Illuminate\Console\Command;
use Throwable;

/**
 * Class ClearInactiveTokenCommand
 * @package Golly\Authority\Commands
 */
class ClearInactiveTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:clear
                            {days=7 : The inactive days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the inactive token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param UserAccessToken $accessToken
     *
     * @return int
     */
    public function handle(UserAccessToken $accessToken)
    {
        try {
            $days = $this->argument('days');
            $accessToken->where([
                ['updated_at', '<', now()->subDays($days)->toDateTimeString()],
            ])->orWhere([
                ['expired_at', '<', now()->toDateTimeString()]
            ])->delete();
        } catch (Throwable $e) {
            return 9;
        }

        return 0;
    }
}
