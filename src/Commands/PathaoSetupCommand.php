<?php

namespace devrkb21\PathaoLaravel\Commands;

use devrkb21\PathaoLaravel\APIBase\PathaoAuth;
use devrkb21\PathaoLaravel\DataDTO\AccessTokenDTO;
use devrkb21\PathaoLaravel\Requests\PathaoAccessTokenRequest;
use devrkb21\PathaoLaravel\Services\DataServiceOutput;
use devrkb21\PathaoLaravel\Services\PathaoHelperFunction;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PathaoSetupCommand extends Command
{
    const TYPE_ASK = 'ask';

    const TYPE_SECRET = 'secret';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathao:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will set up your pathao account.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->checkExistingData()) {
            $this->steps();
        } else {
            $this->errorMessage('You have already registered a token');

            if ($this->confirm('Do you wish to continue? It will reset your existing tokens.', false)) {
                $this->steps();
            }
        }
    }

    private function steps()
    {
        $pathao_client_id = config('pathao.pathao_client_id');
        $pathao_client_secret = config('pathao.pathao_client_secret');

        if (! empty($pathao_client_id) && ! empty($pathao_client_secret)) {
            $this->newLine(1);
            $this->line('Requesting token using Client Credentials...');

            $access_token_request = new PathaoAccessTokenRequest(
                [
                    'client_id' => $pathao_client_id,
                    'client_secret' => $pathao_client_secret,
                ]
            );

            $cred = (new AccessTokenDTO)->fromRequest($access_token_request);
            $response = $this->GET_ACCESS_TOKEN($cred);

            $this->newLine(1);
            $data = $response->getData();
            if ($response->isSuccess()) {
                $this->successMessage('Your secret unique token is '.Arr::get($data, 'secret_token'));
                $this->successMessage('Please update your env value `PATHAO_SECRET_TOKEN` with it.');
            } else {
                $this->errorMessage(Arr::get($data, 'message') ?: 'Authentication failed. Please verify your client credentials.');
            }
            $this->newLine(1);
        } else {
            if (empty($pathao_client_id)) {
                $this->errorMessage('Please provide your Pathao client id in your .env file');
            }
            if (empty($pathao_client_secret)) {
                $this->errorMessage('Please provide your Pathao client secret in your .env file');
            }
        }
    }

    /**
     * It will check if the DB has already one issued token
     */
    private function checkExistingData(): bool
    {
        $data_exist = DB::table(PathaoHelperFunction::getTableName())->count();
        if ($data_exist > 0) {
            return false;
        }

        return true;
    }

    /**
     * It will return a message to command only for success
     *
     * @return void
     */
    private function successMessage(string $message)
    {
        $this->newLine(1);
        $this->info($message);
        $this->newLine(2);
    }

    /**
     * It will return a message to command only for error
     *
     * @return void
     */
    private function errorMessage(string $message)
    {
        $this->newLine(1);
        $this->error($message);
        $this->newLine(2);
    }

    /**
     * This will keep asking the value if the given input is empty
     *
     * @param  mixed  $question
     * @param  mixed  $type
     * @return mixed
     */
    private function askForNonEmptyValue($question, $type)
    {
        $value = '';

        while (empty($value)) {
            if ($type == self::TYPE_ASK) {
                $value = $this->ask($question);
            } elseif ($type == self::TYPE_SECRET) {
                $value = $this->secret($question);
            }

            if (empty($value)) {
                $this->error('Value cannot be empty. Please provide a non-empty value.');
            }
        }

        return $value;
    }

    /**
     * This will issue a access token from Pathao Courier
     */
    private static function GET_ACCESS_TOKEN(array $cred): DataServiceOutput
    {
        return (new PathaoAuth)->getAccessToken($cred);
    }

    /**
     * This will issue a access token from Pathao Courier
     */
    private static function GET_NEW_ACCESS_TOKEN(): DataServiceOutput
    {
        return (new PathaoAuth)->getNewAccesstoken();
    }
}
