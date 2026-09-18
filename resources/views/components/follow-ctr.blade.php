@props(['user'])

<div {{ $attributes }} x-data="{
    following: {{ $user->isFollowedBy(auth()->user()) ? 'true' : 'false'}},
    followersCount: {{ $user->followers()->count() }},
    follow() {
        this.following = !this.following
        axios.post('/follow/{{ $user->id }}')
            .then(res => {
                console.log(res.data)
                this.followersCount = res.data.followersCount
            })
            .catch(err => {
                console.log(err)
            })
    }
}"class="w-[320px] border-l px-8" >
    {{ $slot }}
</div>