<template>
  <div class="payment-plans">
    <div class="container">
      <div class="row desktop d-none d-md-block">
        <div class="col">
          <div class="title">Choose your plan</div>
          <div class="plans">
            <div class="plan" v-for="plan in plans" :key="plan.id">
              <div class="plan-name">{{ plan.name}}</div>
              <button v-if="plan.id == 1" class="btn" @click="choosePlan(plan.id, 2000)">Go {{ plan.name}}</button>
              <button v-if="plan.id == 2" class="btn" @click="choosePlan(plan.id, 2200)">Go {{ plan.name}}</button>
              <button v-if="plan.id == 3" class="btn" @click="choosePlan(plan.id, 2400)">Go {{ plan.name}}</button>

              <div class="price">{{plan.price}}</div>
              <ul>
                <li
                  v-for="item in plan.features"
                  :key="item.id"
                  :class="{ 'not-included': !item.included }"
                >
                  <span class="item">{{ item.name }}</span>
                  <div class="info" v-if="!item.check">{{ item.info }}</div>
                  <i class="fas fa-check" v-if="item.check"></i>
                </li>
              </ul>
            </div>
          </div>
          <div v-if="plans.length"
            class="more-details"
          >&ast;Guided utilisation of the platform´s features to acquire direct knowledge that will serve to aid in project releases, tour planning, fan-base relationship building, and more.</div>
        </div>
      </div>
      <div class="row mobile d-block d-md-none">
        <div class="col">
          <div class="title">Choose your plan</div>

          <div class="mobile-plan-nav">
            <a
              v-for="plan in plans"
              :class="{ 'active': plan.id == selectedPlanId }"
              @click="changePlan(plan.id)"
              :key="plan.id"
            >{{plan.name}}</a>
          </div>
          <ul>
            <li
              v-for="item in selectedPlan.features"
              :key="item.id"
              :class="{ 'not-included': !item.included }"
            >
              <span class="item">{{ item.name }}</span>
              <div class="info" v-if="!item.check">{{ item.info }}</div>
              <i class="fas fa-check" v-if="item.check"></i>
            </li>
          </ul>
          <div v-if="plans.length"
            class="more-details"
          >&ast;Guided utilisation of the platform´s features to acquire direct knowledge that will serve to aid in project releases, tour planning, fan-base relationship building, and more.</div>
          <div class="action-button"></div>
          <div class="action-button">
            <div class="price" v-if="selectedPlan.price">{{selectedPlan.price}}</div>
            <div class="price" v-if="!selectedPlan.price">20$ per month</div>
            <button class="btn" v-if="!selectedPlan.id" @click="choosePlan(selectedPlan.id, 2000)">Go Creator</button>
            <button class="btn" v-if="selectedPlan.id == 1" @click="choosePlan(selectedPlan.id, 2000)">Go {{selectedPlan.name}}</button>
            <button class="btn" v-if="selectedPlan.id == 2" @click="choosePlan(selectedPlan.id, 2200)">Go {{selectedPlan.name}}</button>
            <button class="btn" v-if="selectedPlan.id == 3" @click="choosePlan(plan.id, 2400)">Go {{selectedPlan.name}}</button>
          </div>
        </div>
      </div>
    </div>
    <div>
      <vue-stripe-checkout
        ref="checkoutRef"
        :name="name"
        :description="description"
        :currency="currency"
        :allow-remember-me="false"
        :auto-open-modal="false"
        @done="done"
        @canceled="canceled"
      ></vue-stripe-checkout>
    </div>
  </div>
</template>

<script>
export default {
  name: "choose-plan",
  props: ["creatorParam"],
  data() {
    return {
      next: 3,
      user: {},
      name: "Pay for profiles!",
      description: "Stripe Payment!",
      currency: "USD",
      amount: "",
      plan: "",
      selectedPlanId: 1,
      selectedPlan: {},
      plans: []
    };
  },

  methods: {
    changePlan(id) {
      this.selectedPlanId = id;
      this.plans.map(item => {
        if (item.id === id) {
          this.selectedPlan = item;
          return;
        }
      });
    },
    choosePlan(plan, amount) {
      this.$refs.checkoutRef.$props.amount = amount;
      const { token, args } = this.$refs.checkoutRef.open();
      console.clear();
      this.amount = amount;
      this.plan = plan;
    },

    checkout() {},

    done({ token, args }) {
      if (this.creatorParam) {
        let _this = this;
        let data = {
          stripeToken: token,
          amount: this.amount,
          plan: this.plan
        };

        _this.$emit("creator", _this.creatorParam);
        _this.$emit("validationStep", _this.next);
        _this.$emit("stripe", { stripeCheck: 1, stripeData: data });
      } else {
        let _this = this;
        let data = {
          stripeToken: token,
          amount: this.amount,
          plan: this.plan,
          userId: this.user.id
        };

        axios.post(apiRoute + "/stripe", { userId: this.user.id, stripe: data }).then(response => {
            this.$router.push({ name: "profile.index" });
          bus.$emit('refreshSubscription', 1)
          }).catch(error => {});
      }
    },

    getUser() {
        let _this = this;
        axios.get(apiRoute + "/user", this.$store.getters["auth/token"]).then(response => {
          _this.user = response.data.data;
        }).catch(error => {

        });
    },

    getPlans() {
      if (this.creatorParam) {
        let _this = this;
        axios.get(apiRoute + "/plans").then(response => {
          _this.plans = response.data.data;
        }).catch(error => {

        });
      }else{
        let _this = this;
        axios.get(apiRoute + "/user/plans", this.$store.getters["auth/token"]).then(response => {
          _this.plans = response.data.data;
        }).catch(error => {

        });
      }


    },
    canceled() {
      console.clear();
    }
  },

  mounted() {
    this.plans = [];
    this.getPlans();
    this.changePlan(this.selectedPlanId);
    if (!this.creatorParam) {
      this.getUser();
    }
  }
};
</script>

<style scoped lang="scss">
.container {
  @media (min-width: 768px) {
    padding-left: 15px;
  }

  @media (max-width: 991px) {
    max-width: 100%;
  }
}
.title {
  text-align: center;
  font-size: 2.5rem;
  font-family: EncodeSansSemiBold;
  padding: 30px 0;
  @media (max-width: 767px) {
    font-size: 1.8rem;
  }
}
.plans {
  display: flex;
  justify-content: space-between;
  .plan {
    width: 30%;
    background-color: #1a1a1a;
    border-radius: 20px;
    .plan-name {
      text-align: center;
      font-size: 1.2rem;
      font-family: EncodeSansSemiBold;
      text-transform: uppercase;
      padding: 15px 0;
    }
    .btn {
      display: block;
      margin: 0 auto;
      background-color: #fff;
      transition: 0.3s;
      border-radius: 20px;
      width: 80%;
      font-size: 1rem;
      font-family: EncodeSansMedium;
      &:hover {
        opacity: 0.6;
        transition: 0.3s;
      }
    }
    .price {
      margin-top: 15px;
      text-align: center;
      font-size: 0.8rem;
      font-family: EncodeSansSemiBold;
    }
    ul {
      list-style: none;
      margin: 0;
      margin-top: 25px;
      padding: 0;
      padding-bottom: 20px;
      li {
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0px;
        margin: 0px 20px;
        border-bottom: 1px dotted #fff;
        .item {
          font-size: 0.8rem;
        }
        .info {
          font-family: EncodeSansSemiBold;
          font-size: 1rem;
        }
        &:last-child {
          border-bottom: none;
        }
        &.not-included {
          color: #424242;
          border-bottom: 1px dotted #424242;
          &:last-child {
            border-bottom: none;
          }
        }
      }
    }
  }
}
.mobile {
  padding-bottom: 100px;
  ul {
    list-style: none;
    margin: 0;
    margin-top: 25px;
    padding: 0;
    padding-bottom: 20px;
    li {
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0px;
      margin: 0px 20px;
      border-bottom: 1px dotted #fff;
      .item {
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-right: 10px;
      }
      .info {
        font-family: EncodeSansSemiBold;
        font-size: 1rem;
      }
      &:last-child {
        border-bottom: none;
      }
      &.not-included {
        color: #424242;
        border-bottom: 1px dotted #424242;
        &:last-child {
          border-bottom: none;
        }
      }
    }
  }
  .action-button {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #424242;
    .price {
      margin-top: 10px;
      text-align: center;
      font-size: 0.8rem;
      font-family: EncodeSansSemiBold;
    }
    .btn {
      display: block;
      margin: 10px auto;
      background-color: #fff;
      width: 80%;
      border-radius: 20px;
      font-size: 1rem;
      font-family: EncodeSansMedium;
      &:hover {
        opacity: 0.7;
        transition: 0.3s;
      }
    }
  }
}
.mobile-plan-nav {
  display: flex;
  justify-content: center;
  align-items: center;
  a {
    text-transform: uppercase;
    color: #fff;
    transition: opacity 0.3s;
    padding-bottom: 5px;
    font-size: 0.8rem;
    &:hover {
      opacity: 0.5;
      transition: opacity 0.3s;
    }
    &:nth-child(2) {
      margin: 0 15px;
    }
    &.active {
      border-bottom: 2px solid #fff;
      opacity: 1;
    }
  }
}

.more-details {
  color: #fff;
  font-size: 0.8rem;
  margin-top: 25px;
  @media (min-width: 768px) {
    width: 50%;
  }
  @media (max-width: 767px) {
    padding: 0 20px;
    color: #424242;
  }
}

@media (max-width: 767px) {
  .action-button {
    bottom: 64px !important;
  }
}
</style>
