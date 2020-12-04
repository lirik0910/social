<?php


namespace App\Notifications\CharityOrganization;


use App\Libraries\GraphQL\AbstractNotification;
use App\Models\CharityOrganization;
use Illuminate\Bus\Queueable;

class CharityOrganizationModerationResult extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'charity_organization.moderation';

    protected $charity_organization;

    /**
     * Create a new notification instance.
     *
     * @param CharityOrganization $charity_organization
     * @return void
     */
    public function __construct(CharityOrganization $charity_organization)
    {
        $this->charity_organization = $charity_organization;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        return [
            'info' => [
                'charity_organization_id' => (string) $this->charity_organization->id,
                'charity_organization_name' => $this->charity_organization->name,
                'charity_organization_moderation_status' => $this->charity_organization->moderation_status,
                'charity_organization_moderation_declined_reason' => $this->charity_organization->moderation_declined_reason,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
